<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Status_lib extends MY_lib
{

    private $status_map = array(
        'booked' => 'forward',
        'pending pickup' => 'forward',
        'in transit' => 'forward',
        'out for delivery' => 'forward',
        'exception' => 'forward',
        'delivered' => 'forward',
        'rto' => 'rto',
        'lost' => 'forward',
        'damaged' => 'forward',
        'cancelled' => 'cancelled',
    );

    protected $shipment = false;

    protected $shipment_id = false;

    protected $status_data = array();

    protected $shipment_update = array();

    public function __construct($config = array())
    {
        parent::__construct();
        $this->CI->load->library('admin/shipping_lib');
        $this->CI->load->library('wallet_lib');

        if (!empty($config['shipment_id']))

            $this->setShipmentID($config['shipment_id']);

        if (!empty($config))
            $this->status_data = $config;
    }

    function updateStatus()
    {
        if (!$this->shipment)
            return false;

        if (empty($this->status_data['ship_status']))
            return false;

        $new_status = strtolower($this->status_data['ship_status']);

        switch ($new_status) {
            case 'in transit':
                $this->CI->load->library('pickups_lib');
                $this->CI->pickups_lib->markPickedUP($this->shipment->id, $this->shipment->user_id);
                $this->savePickupTime();
                break;
            case 'delivered':
                $this->saveDeliveredTime();
                do_action('shipment.setcompletestatus', $this->shipment_id);
                break;
            case 'rto delivered':
            case 'rto lost':
            case 'rto damaged':
            case 'rto in transit':
            case 'rto':
                $this->processRTOShipment();
                switch ($new_status) {
                    case 'rto in transit':
                        $this->shipment_update['rto_status'] = 'in transit';
                        do_action('shipment.setcompletestatus', $this->shipment_id);
                        break;
                    case 'rto delivered':
                        $this->shipment_update['rto_status'] = 'delivered';
                        break;
                    case 'rto lost':
                        $this->shipment_update['rto_status'] = 'lost';
                        break;
                    case 'rto damaged':
                        $this->shipment_update['rto_status'] = 'damaged';
                        break;
                    default:
                }

                if (!empty($this->status_data['rto_awb'])) $this->shipment_update['rto_awb'] = $this->status_data['rto_awb'];

                $new_status = 'rto';
                break;
            case 'pending pickup':
            case 'out for delivery':
            case 'booked':
            case 'cancelled':
            case 'lost':
            case 'damaged':
            case 'exception':
                break;
            default:
                return false;
        }

        $this->shipment_update['ship_status'] = $new_status;

        if (!empty($this->status_data['event_time'])) {
            $this->shipment_update['status_updated_at'] = $this->status_data['event_time'];
        }

        if ($this->shipment->ship_status == 'booked' && $new_status == 'pending pickup')
            return false;

        //********************************update handover flag**************/
        if (!in_array($this->shipment_update['ship_status'], array('cancelled', 'new', 'booked', 'pending pickup'))) {
            $this->shipment_update['is_hand_over'] = 'yes';
        }
        if (in_array($this->shipment_update['ship_status'], array('cancelled', 'new', 'booked', 'pending pickup'))) {
            $this->shipment_update['is_hand_over'] = 'no';
        }
        $this->shipment->ship_status = $new_status;
        $this->updateShipment();

        $new_status_lib = new Status_lib();
        $new_status_lib->setShipmentID($this->shipment->id);
        $new_status_lib->applyAccurateCharges();

        return true;
    }

    private function updateShipment()
    {
        if (!$this->shipment)
            return false;

        if (empty($this->shipment_update))
            return false;

        $this->CI->shipping_lib->update($this->shipment->id, $this->shipment_update);
    }

    private function savePickupTime()
    {
        if (!$this->shipment)
            return false;

        //shipment is already in transit
        if (in_array($this->shipment->ship_status, array('in transit')))
            return false;

        //shipment status is not booked or pending pickup //
        if (!in_array($this->shipment->ship_status, array('booked', 'pending pickup')))
            return false;

        //shipment have pickup time 
        if (!empty($this->shipment->pickup_time))
            return false;


        $this->shipment_update['pickup_time'] = time();

        return true;
    }

    private function saveDeliveredTime()
    {
        if (!$this->shipment)
            return false;

        //shipment is already delivered
        if (in_array($this->shipment->ship_status, array('delivered')))
            return false;

        //shipment have delivered time
        if (!empty($this->shipment->delivered_time))
            return false;

        $this->shipment_update['delivered_time'] = time();

        return true;
    }

    private function processRTOShipment()
    {
        if (!$this->shipment)
            return false;

        //check if rto charges are already applied
        if ($this->shipment->rto_charges > 0)
            return false;

        if ((int)$this->shipment->base_rto_freight==0)
            return false;

        //get user details
        $this->CI->load->library('user_lib');
        $user = $this->CI->user_lib->getByID($this->shipment->user_id);

        if (empty($user))
            return false;

        $this->CI->load->library('plans_lib');
        $plan = $this->CI->plans_lib->getPlanByName($user->pricing_plan);

        $plan_type = $plan->plan_type;

        if ($this->shipment->rto_charges <= 0) {
            if ($plan_type != 'per_dispatch') {
                $fees = $this->debitRTOCharges();
                $this->shipment_update['rto_charges'] = $fees;
            } else {
                $this->shipment_update['rto_charges'] = $this->shipment->base_rto_freight;
            }
        }

        if ($this->shipment->cod_reverse_amount <= 0) {
            $this->refundCODCharges();
            $this->shipment_update['cod_reverse_amount'] = $this->shipment->cod_fees;
        }

        if ($plan_type != 'per_dispatch') {
            if ((int)$this->shipment->extra_weight_charges > 0 && $this->shipment->rto_extra_weight_charges <= 0) {
                $this->debitRTOExtraWeightCharges();
                $this->shipment_update['rto_extra_weight_charges'] = $this->shipment->extra_weight_charges;
            }
        }

        $this->shipment_update['rto_date'] = time();

        return true;
    }


    function setShipmentID($value = false)
    {
        if (!$value)
            return false;

        $this->shipment_id = $value;
        $this->shipment = $this->CI->shipping_lib->getByID($this->shipment_id);
    }

    function applyAccurateCharges()
    {
        if (!$this->shipment)
            return false;

        $this->shipment->ship_status = strtolower($this->shipment->ship_status);

        if (!array_key_exists($this->shipment->ship_status, $this->status_map))
            return false;

        $status_to_charge = $this->status_map[$this->shipment->ship_status];

        switch ($status_to_charge) {
            case 'forward':
                $this->applyAccurateForwardCharges();
                break;
            case 'rto':
                $this->applyAccurateRTOCharges();
                break;
            case 'cancelled':
                $this->applyCancelledCharges();
                break;
            default:
                return false;
        }

        $this->updateShipment();
    }

    private function applyAccurateForwardCharges()
    {
        if (!$this->shipment)
            return false;


        $this->shipment_update['rto_date'] = '0';

        if ($this->shipment->fees_refunded  == '1') {
            $this->debitFreightCharges();

            if ($this->shipment->cod_fees > 0) {
                $this->debitCODCharges();
            }
            $this->shipment_update['fees_refunded'] = '0';
        }

        if ($this->shipment->rto_charges > 0) {
            $this->refundRTOCharges();
            $this->shipment_update['rto_charges'] = '0';
        }

        if ($this->shipment->cod_reverse_amount > 0) {
            $this->debitCODCharges();
            $this->shipment_update['cod_reverse_amount'] = '0';
        }

        if ($this->shipment->rto_extra_weight_charges > 0) {
            $this->refundRTOExtraWeightCharges();
            $this->shipment_update['rto_extra_weight_charges'] = '0';
        }


        return true;
    }

    private function applyAccurateRTOCharges()
    {
        if (!$this->shipment)
            return false;

        //get user details
        $this->CI->load->library('user_lib');
        $user = $this->CI->user_lib->getByID($this->shipment->user_id);

        if (empty($user))
            return false;

        $this->CI->load->library('plans_lib');
        $plan = $this->CI->plans_lib->getPlanByName($user->pricing_plan);

        if (empty($plan))
            return false;

        $plan_type = $plan->plan_type;

        $this->shipment_update['delivered_time'] = '0';

        if ($this->shipment->fees_refunded  == '1') {
            $this->debitFreightCharges();

            if ($this->shipment->cod_fees > 0) {
                $this->debitCODCharges();
            }
            $this->shipment_update['fees_refunded'] = '0';
        }

        if ($this->shipment->rto_charges <= 0) {
            if ($plan_type != 'per_dispatch') {
                $fees = $this->debitRTOCharges();
                $this->shipment_update['rto_charges'] = $fees;
            } else {
                $this->shipment_update['rto_charges'] = $this->shipment->base_rto_freight;
            }
        }

        if ($this->shipment->cod_reverse_amount <= 0) {
            $this->refundCODCharges();
            $this->shipment_update['cod_reverse_amount'] = $this->shipment->cod_fees;
        }

        if ($plan_type != 'per_dispatch') {
            if ((int)$this->shipment->extra_weight_charges > 0 && $this->shipment->rto_extra_weight_charges <= 0) {
                $this->debitRTOExtraWeightCharges();
                $this->shipment_update['rto_extra_weight_charges'] = $this->shipment->extra_weight_charges;
            }
        }

        return true;
    }

    private function applyCancelledCharges()
    {
        if (!$this->shipment)
            return false;

        if ($this->shipment->fees_refunded  == '0') {
            $this->refundFreightCharges();

            if ($this->shipment->cod_fees > 0) {
                $this->refundCODCharges();
            }
            $this->shipment_update['fees_refunded'] = '1';
        }

        if ($this->shipment->rto_charges > 0) {
            $this->refundRTOCharges();
            $this->shipment_update['rto_charges'] = '0';
        }

        if ($this->shipment->cod_reverse_amount > 0) {
            $this->debitCODCharges();
            $this->shipment_update['cod_reverse_amount'] = '0';
        }

        return true;
    }

    //debit freight Charges
    private function debitFreightCharges($amount = false)
    {
        if (!$this->shipment)
            return false;

        $fees = $this->shipment->courier_fees;
        if ($amount)
            $fees = $amount;

        if ($this->shipment->all_freight_reversed == 1)
            return false;

        $wallet = new Wallet_lib(array('user_id' => $this->shipment->user_id));
        $wallet->setAmount($fees);
        $wallet->setTransactionType('debit');
        $wallet->setNotes('Freight Charges');
        $wallet->setRefID($this->shipment_id);
        $wallet->setTxnFor('shipment');
        $wallet->setTxnRef('freight');
        $wallet->creditDebitWallet();
    }

    //refund freight Charges
    private function refundFreightCharges($amount = false)
    {
        if (!$this->shipment)
            return false;

        $fees = $this->shipment->courier_fees;
        if ($amount)
            $fees = $amount;

        $wallet = new Wallet_lib(array('user_id' => $this->shipment->user_id));
        $wallet->setAmount($fees);
        $wallet->setTransactionType('credit');
        $wallet->setNotes('Freight Charges');
        $wallet->setRefID($this->shipment_id);
        $wallet->setTxnFor('shipment');
        $wallet->setTxnRef('freight');
        $wallet->creditDebitWallet();
    }

    //Apply COD Charges
    private function debitCODCharges($amount = false)
    {
        if (!$this->shipment)
            return false;

        if ($this->shipment->all_freight_reversed == 1)
            return false;

        $fees = $this->shipment->cod_fees;
        if ($amount)
            $fees = $amount;

        if ($fees > 0) {
            $wallet = new Wallet_lib(array('user_id' => $this->shipment->user_id));
            $wallet->setAmount($fees);
            $wallet->setTransactionType('debit');
            $wallet->setNotes('COD Charges');
            $wallet->setRefID($this->shipment_id);
            $wallet->setTxnFor('shipment');
            $wallet->setTxnRef('cod');
            $wallet->creditDebitWallet();
        }
    }

    //apply rto charges
    private function debitRTOCharges($amount = false)
    {
        if (!$this->shipment)
            return false;

        if ($this->shipment->all_freight_reversed == 1)
            return false;
            
        if ($this->shipment->rto_fee_refund == 1)
            return false;

        //get user details
        $this->CI->load->library('user_lib');
        $user = $this->CI->user_lib->getByID($this->shipment->user_id);

        if (empty($user))
            return false;

        //get order details
        $this->CI->load->library('orders_lib');
        $order = $this->CI->orders_lib->getByID($this->shipment->order_id);

        if (empty($order))
            return false;

        //get warehouse details
        $this->CI->load->library('warehouse_lib');
        $warehouse = $this->CI->warehouse_lib->getByID($this->shipment->warehouse_id);

        if (empty($warehouse))
            return false;

        $this->CI->load->library('pricing_lib');
        $pricing = new Pricing_lib();
        $pricing->setPlan($user->pricing_plan);
        $pricing->setCourier($this->shipment->courier_id);
        $pricing->setBaseFreight($this->shipment->base_freight);
        $pricing->setRtoFreight($this->shipment->base_rto_freight);
        $pricing->setExtraFreight($this->shipment->base_add_weight_freight);
        $pricing->setOrigin($warehouse->zip);
        $pricing->setDestination($order->shipping_zip);
        $pricing->setType($order->order_payment_type);
        $pricing->setAmount($order->order_amount);
        $pricing->setWeight($order->package_weight);
        $pricing->setLength($order->package_length);
        $pricing->setBreadth($order->package_breadth);
        $pricing->setHeight($order->package_height);

        $shipping_cost = $pricing->calculateCost();

        $fees = $shipping_cost['total_rto_charges'];
        if ($fees > 0) {
            $wallet = new Wallet_lib(array('user_id' => $this->shipment->user_id));
            $wallet->setAmount($fees);
            $wallet->setTransactionType('debit');
            $wallet->setNotes('RTO Freight Charges');
            $wallet->setRefID($this->shipment_id);
            $wallet->setTxnFor('shipment');
            $wallet->setTxnRef('rto_freight');
            $wallet->creditDebitWallet();
        }

        return $fees;
    }


    //refund RTO Charges
    private function refundRTOCharges($amount = false)
    {
        if (!$this->shipment)
            return false;

        $fees = $this->shipment->rto_charges;
        if ($amount)
            $fees = $amount;

        $wallet = new Wallet_lib(array('user_id' => $this->shipment->user_id));
        $wallet->setAmount($fees);
        $wallet->setTransactionType('credit');
        $wallet->setNotes('RTO Freight Charges');
        $wallet->setRefID($this->shipment_id);
        $wallet->setTxnFor('shipment');
        $wallet->setTxnRef('rto_freight');
        $wallet->creditDebitWallet();
    }

    private function refundCODCharges($amount = false)
    {
        if (!$this->shipment)
            return false;

        $fees = $this->shipment->cod_fees;
        if ($amount)
            $fees = $amount;

        $wallet = new Wallet_lib(array('user_id' => $this->shipment->user_id));
        $wallet->setAmount($fees);
        $wallet->setTransactionType('credit');
        $wallet->setNotes('COD Charges');
        $wallet->setRefID($this->shipment_id);
        $wallet->setTxnFor('shipment');
        $wallet->setTxnRef('cod');
        $wallet->creditDebitWallet();
    }


    private function refundRTOExtraWeightCharges($amount = false)
    {
        if (!$this->shipment)
            return false;

        $fees = $this->shipment->rto_extra_weight_charges;

        if ($amount)
            $fees = $amount;

        $wallet = new Wallet_lib(array('user_id' => $this->shipment->user_id));
        $wallet->setAmount($fees);
        $wallet->setTransactionType('credit');
        $wallet->setNotes('RTO Extra Weight Charges');
        $wallet->setTxnFor('shipment');
        $wallet->setRefID($this->shipment->id);
        $wallet->setTxnRef('rto_extra_weight');
        $wallet->creditDebitWallet();
    }

    private function debitRTOExtraWeightCharges($amount = false)
    {
        if (!$this->shipment)
            return false;

        $fees = $this->shipment->extra_weight_charges;

        if ($amount)
            $fees = $amount;

        if ($this->shipment->all_freight_reversed == 1)
            return false;

        $wallet = new Wallet_lib(array('user_id' => $this->shipment->user_id));
        $wallet->setAmount($fees);
        $wallet->setTransactionType('debit');
        $wallet->setNotes('RTO Extra Weight Charges');
        $wallet->setTxnFor('shipment');
        $wallet->setRefID($this->shipment->id);
        $wallet->setTxnRef('rto_extra_weight');
        $wallet->creditDebitWallet();
    }

    /*
    * Check if a order is from Shopify Channel or not
    * return true or false.
    */
    function check_order_channel($cid)
    {
        if (!empty($cid)) {
            $this->CI->load->model('channels_model');
            $details =  $this->CI->channels_model->getByID($cid);

            if (trim($details->channel) == 'shopify')
                return $details; // ->api_field_1 ; // return the URL of the channel 
            else
                return false;
        }
        return false;
    }
}
