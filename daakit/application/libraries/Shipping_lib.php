<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Shipping_lib extends MY_lib
{
    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('shipping_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->shipping_model, $method)) {
            throw new Exception('Undefined method shipping_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->shipping_model, $method], $arguments);
    }

    function updateShipmentMessage($shipment_id = false, $message = false)
    {
        if (!$shipment_id)
            return false;

        $shipment = $this->getByID($shipment_id);
        if (!empty($shipment->awb_number))
            return false;

        $update = array(
            'message' => $message,
        );
        $this->update($shipment_id, $update);
        return true;
    }

    function processShipment($shipment_id = false, $generate_for_api = false, $is_price_calculate = true, $selected_courier = 'autoship')
    {
        $this->error = '';

        if (!$shipment_id) {
            $this->error = 'Invalid Shipment ID';
            return false;
        }

        //get shipping details for this order
        $shipment = $this->getByID($shipment_id);
        if (empty($shipment)) {
            $this->error = 'Shipment Not Found';
            return false;
        }

        $awb_data = $this->generate_awb($shipment_id, $is_price_calculate);

        if ((empty($awb_data)) && ($generate_for_api) && ($selected_courier != 'autoship')) {
            return false;
        }

        if (!empty($awb_data))
            return $awb_data;

        if (!$generate_for_api) {
            $this->routeProcessingShipment($shipment_id);
            return false;
        }

        while ($this->routeProcessingShipment($shipment_id, true)) {
            $awb_data = $this->generate_awb($shipment_id, $is_price_calculate);
            if (!empty($awb_data))
                return $awb_data;
        }

        return $awb_data;
    }

    function generate_awb($shipment_id = false, $is_price_calculate = true)
    {
        if (!$shipment_id) {
            $this->error = 'Invalid Shipment ID';
            return false;
        }

        //get shipping details for this order
        $shipment = $this->getByID($shipment_id);
        if (empty($shipment)) {
            $this->error = 'Shipment Not Found';
            return false;
        }
        // courier tranfer to another courier---New
        $shipment->courier_id=!empty($shipment->actual_courier_id) ? $shipment->actual_courier_id : $shipment->courier_id;

        if ($shipment->order_type != 'ecom') {
            $this->updateShipmentMessage($shipment_id, 'Invalid Order Type');
            $this->error = 'Already Booked';
            return false;
        }

        if ($shipment->awb_number != '') {
            $this->updateShipmentMessage($shipment_id, 'Invalid Order Type');
            $this->error = 'Already Booked';
            return false;
        }

        //get order details from db
        $this->CI->load->library('orders_lib');
        $order = $this->CI->orders_lib->getByID($shipment->order_id);

        if (empty($order)) {
            $this->updateShipmentMessage($shipment_id, 'Order Not Found');
            $this->error = 'Order not found';
            return false;
        }

        //get order product name
        $products = $this->CI->orders_lib->getOrderProducts($order->id);

        // applied weight code start
        $applied_flag = 0;
        $weight_applied_details = array();
        if ((!empty($products)) && (count($products) == 1)) {
            $this->CI->load->library('products_lib');
            $products_details_data = $this->CI->products_lib->getOrderProductdetails($order->user_id, $products);

            if (!empty($products_details_data)) {
                $order = $this->CI->products_lib->getChargebleData($products_details_data, $order);
                $applied_flag = 1;
                $weight_applied_details['user_id'] = $order->user_id;
                $weight_applied_details['shipment_id'] = $shipment_id;
            }
        }
        // applied weight code end

        if (empty($products)) {
            $this->updateShipmentMessage($shipment_id, 'Product information is unavailable');
            $this->error = 'Product information is unavailable';
            return false;
        }

        //check user wallet balance
        $this->CI->load->library('wallet_lib');

        if ($is_price_calculate && !$this->CI->wallet_lib->checkUserCanShip($order->user_id)) {
            $this->updateShipmentMessage($shipment_id, 'Your wallet balance is insufficient.');
            $this->error = 'Your wallet balance is insufficient.';
            return false;
        }

        //get courier details
        $this->CI->load->library('courier_lib');
        $courier = $this->CI->courier_lib->getByID($shipment->courier_id);

        if (empty($courier)) {
            $this->updateShipmentMessage($shipment_id, 'Courier service is not recognized');
            $this->error = 'Courier service is not recognized';
            return false;
        }

        if (defined('skip_inactive') && skip_inactive == 'yes') {
        } elseif ($courier->status != '1') {
            $this->updateShipmentMessage($shipment_id, 'The courier service is currently inactive');
            $this->error = 'The courier service is currently inactive';
            return false;
        }

        if (strtolower($order->order_payment_type) == 'reverse') {
            if ($order->qccheck == '1' && !$courier->reverse_qc_pickup) {
                $this->updateShipmentMessage($shipment_id, 'The courier service is not available in this area');
                $this->error = 'The courier service is not available in this area';
                return false;
            }
        } else {
            if ($courier->reverse_pickup == '1' || $courier->reverse_qc_pickup == '1') {
                $this->updateShipmentMessage($shipment_id, 'The courier service is not available in this area');
                $this->error = 'The courier service is not available in this area';
                return false;
            }
        }

        //get warehouse details
        $this->CI->load->library('warehouse_lib');
        if ($shipment->warehouse_id != '0') {
            $warehouse = $this->CI->warehouse_lib->getByID($shipment->warehouse_id);
        } else {
            $warehouse = $this->CI->warehouse_lib->getUserWarehouse($order->user_id);
        }

        if (empty($warehouse) || empty($warehouse->zip)) {
            $this->error = 'Warehouse information is not available';
            $this->updateShipmentMessage($shipment_id, 'Warehouse information is not available');
            return false;
        }

        $rto_warehouse = false;
        $is_rto_different = false;

        if ($shipment->rto_warehouse_id > 0 && $shipment->rto_warehouse_id != $shipment->warehouse_id) {
            $is_rto_different = true;
            $rto_warehouse = $this->CI->warehouse_lib->getByID($shipment->rto_warehouse_id);
        }
        //check sdd/ndd pincode
        if(strtolower($courier->code=='sdd_ndd'))
        {
            $this->CI->load->library('pincode_lib');
            $pickup_pincode_details = $this->CI->pincode_lib->checkPickupServiceByCourier($warehouse->zip, $courier->id);
            if (!empty($pickup_pincode_details)) {
                $pickup_sdd_code = $pickup_pincode_details->sdd_code;
            }
            $delivery_pincode_details = $this->CI->pincode_lib->checkPincodeServiceByCourier($order->shipping_zip, $courier->id, $order->order_payment_type);
            if (!empty($delivery_pincode_details)) {
                $delivery_sdd_code = $delivery_pincode_details->sdd_code;
            }
            if(empty($pickup_sdd_code) || empty($delivery_sdd_code)){
                $this->error = 'SDD/NDD code is not available.';
                $this->updateShipmentMessage($shipment_id, 'SDD/NDD code is not available');
                return false;
            }
            if((!empty($pickup_sdd_code) && !empty($delivery_sdd_code)) && ($pickup_sdd_code!=$delivery_sdd_code)){
                $this->error = 'SDD/NDD code are mismatched.';
                $this->updateShipmentMessage($shipment_id, 'SDD/NDD code are mismatched');
                return false;
            }
        }
        //get user details
        $this->CI->load->library('user_lib');
        $user = $this->CI->user_lib->getByID($order->user_id);

        $courier_shipment_id = 'DKT/' . $shipment->id;
        $shipment_data = array(
            'order' => array(
                'id' => $order->id,
                'user_id' => $order->user_id,
                'seller_order_id' => $order->order_no,
                'payment_method' => strtolower($order->order_payment_type),
                'shipment_id' => $courier_shipment_id,
                'ship_id' => $shipment->id,
                'total' => $order->order_amount,
                'date' => $order->order_date,
                'weight' => (!empty($order->package_weight) && floor($order->package_weight / 500) >= 1) ? $order->package_weight : 500,
                'length' => !empty($order->package_length) ? $order->package_length : '10',
                'height' => !empty($order->package_height) ? $order->package_height : '10',
                'breadth' => !empty($order->package_breadth) ? $order->package_breadth : '10',
                'shipping_charges' => !empty($order->shipping_charges) ? $order->shipping_charges : '0',
                'cod_charges' => !empty($order->cod_charges) ? $order->cod_charges : '0',
                'discount' => !empty($order->package_breadth) ? $order->discount : '0',
                'tax_amount' => !empty($order->tax_amount) ? $order->tax_amount : '0',
                'shipment_created' => $shipment->created,
                'essential_order' => $shipment->essential_order,
                'dg_order' => $shipment->dg_order,
                'qccheck' => $order->qccheck,
                'day_name' => strtolower(date('D'))
            ),
            'customer' => array(
                'customer_company' => $order->shipping_company_name,
                'name' => $order->shipping_fname . ' ' . $order->shipping_lname,
                'address' => remove_special_charcater($order->shipping_address),
                'address_2' => remove_special_charcater($order->shipping_address_2),
                'city' => $order->shipping_city,
                'state' => $order->shipping_state,
                'country' => $order->shipping_country,
                'zip' => $order->shipping_zip,
                'phone' => $order->shipping_phone
            ),
            'pickup' => array(
                'seller_company' => $user->company_name,
                'warehouse_id' => $warehouse->id,
                'name' => $warehouse->name,
                'contact_name' => $warehouse->contact_name,
                'address_1' => remove_special_charcater($warehouse->address_1),
                'address_2' => remove_special_charcater($warehouse->address_2),
                'city' => $warehouse->city,
                'state' => $warehouse->state,
                'country' => $warehouse->country,
                'zip' => $warehouse->zip,
                'phone' => $warehouse->phone,
                'gst' => $warehouse->gst_number,
                'fship_warehouse_id' => $warehouse->fship_warehouse_id,
            ),
            'courier' => array(
                'id' => (!empty($courier->aggregator_courier_id) ? $courier->aggregator_courier_id : $courier->id),
                'courier_type' => $courier->courier_type
            )
        );

        if ((isset($order->seller_applied_weight)) && (!empty($order->seller_applied_weight))) {
            $shipment_data['order']['weight'] = (!empty($order->seller_applied_weight) && floor($order->seller_applied_weight / 500) >= 1) ? $order->seller_applied_weight : 500;
            $weight_applied_details['weight'] = $shipment_data['order']['weight'];
        }
        if ((isset($order->seller_applied_length)) && (!empty($order->seller_applied_length))) {
            $shipment_data['order']['length'] = $order->seller_applied_length;
            $weight_applied_details['length'] = $shipment_data['order']['length'];
        }
        if ((isset($order->seller_applied_breadth)) && (!empty($order->seller_applied_breadth))) {
            $shipment_data['order']['breadth'] = $order->seller_applied_breadth;
            $weight_applied_details['breadth'] = $shipment_data['order']['breadth'];
        }
        if ((isset($order->seller_applied_height)) && (!empty($order->seller_applied_height))) {
            $shipment_data['order']['height'] = $order->seller_applied_height;
            $weight_applied_details['height'] = $shipment_data['order']['height'];
        }

        $warehouse_data = array(
            'seller_company' => $user->company_name,
            'warehouse_id' => $warehouse->id,
            'user_id' => $warehouse->user_id,
            'email' => $warehouse->email,
            'phone' => $warehouse->phone,
            'city' => $warehouse->city,
            'state' => $warehouse->state,
            'pin' => $warehouse->zip,
            'address_1' => remove_special_charcater($warehouse->address_1),
            'address_2' => remove_special_charcater($warehouse->address_2),
            'contact_person' => $warehouse->contact_name,
            'name' => 'delta_' . $warehouse->id,
            'gst' => $warehouse->gst_number,
            'fship_warehouse_id' => $warehouse->fship_warehouse_id,
            'courier' => $courier
        );

        if ($is_rto_different) {
            $shipment_data['rto'] = array(
                'warehouse_id' => $rto_warehouse->id,
                'user_id' => $rto_warehouse->user_id,
                'name' => $rto_warehouse->name,
                'email' => $rto_warehouse->email,
                'contact_name' => $rto_warehouse->contact_name,
                'address_1' => remove_special_charcater($rto_warehouse->address_1),
                'address_2' => remove_special_charcater($rto_warehouse->address_2),
                'city' => $rto_warehouse->city,
                'state' => $rto_warehouse->state,
                'country' => $rto_warehouse->country,
                'zip' => $rto_warehouse->zip,
                'phone' => $rto_warehouse->phone,
                'gst' => $rto_warehouse->gst_number
            );
        }

        $categories = [];
        $ordercategories = $this->CI->orders_lib->getOrdercategories();
        foreach ($ordercategories as $key => $value) {
            $categories[$value->id] = $value->categories_name;
        }

        foreach ($products as $product) {
            $product_name = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $product->product_name);
            $shipment_data['products'][] = array(
                'id' => $product->id,
                'name' => $product_name,
                'qty' => $product->product_qty,
                'sku' => $product->product_sku,
                'weight' => $product->product_weight,
                'price' => $product->product_price
            );
        }
        //calculate shipping fees
        $this->CI->load->library('pricing_lib');

        $pricing = new Pricing_lib();
        $pricing->setPlan($user->pricing_plan);
        $pricing->setCourier($courier->id);
        $pricing->setShipment($shipment_id);
        $pricing->setOrigin($shipment_data['pickup']['zip']);
        $pricing->setDestination($shipment_data['customer']['zip']);
        $pricing->setType($shipment_data['order']['payment_method']);
        $pricing->setAmount($shipment_data['order']['total']);
        $pricing->setWeight($shipment_data['order']['weight']);
        $pricing->setLength($shipment_data['order']['length']);
        $pricing->setBreadth($shipment_data['order']['breadth']);
        $pricing->setHeight($shipment_data['order']['height']);

        $shipping_cost = $pricing->calculateCost();

        if (empty($shipping_cost['total'])) {
            do_action('log.create', 'shipment', [
                'action' => 'shipping_cost_empty',
                'ref_id' => $shipment_id,
                'courier' => $courier->name,
                'courier_id' => $courier->id,
                'data' => array(
                    'pricing_plan' => $user->pricing_plan,
                    'shipment_data' => $shipment_data,
                    'shipping_cost' => $shipping_cost
                )
            ]);
        }

        if ($is_price_calculate) {
            if (empty($shipping_cost['total'])) {
                $this->error = 'Freight cost is unavailable for calculation';
                $this->updateShipmentMessage($shipment_id, $this->error);
                return false;
            }
        }
        //calculate shipping fees
        if(!empty($courier->aggregator_courier_id))
            $courier->display_name='Fship';

        $awb = array();
        switch (strtolower($courier->display_name)) {
            case 'fship': //fship
                $this->CI->load->library('shipping/aggregator/Fship');
                $fship = new Fship();
                if (!$awb = $fship->createOrder($shipment_data)) {
                    $this->error = $fship->get_error();
                    $this->updateShipmentMessage($shipment_id, $fship->get_error());
                    return false;
                }
            break;
            case 'pickndel': 
                $this->CI->load->library('shipping/Pickndel');
                $pickndel = new Pickndel();
                if (!$awb = $pickndel->createOrder($shipment_data)) {
                    $this->error = $pickndel->get_error();
                    $this->updateShipmentMessage($shipment_id, $pickndel->get_error());
                    return false;
                }
            break;
            case 'purpledrone': 
                $this->CI->load->library('shipping/Purpledrone');
                $purpledrone = new Purpledrone();
                if (!$awb = $purpledrone->createOrder($shipment_data)) {
                    $this->error = $purpledrone->get_error();
                    $this->updateShipmentMessage($shipment_id, $purpledrone->get_error());
                    return false;
                }
            break;
            case 'delhivery': //Delhivery 
                $this->CI->load->library('shipping/Delhivery');
                $delhivery = new Delhivery(array('mode' => $courier->code));
                if (!$awb = $delhivery->createOrder($shipment_data)) {
                    $this->error = $delhivery->get_error();
                    $this->updateShipmentMessage($shipment_id, $delhivery->get_error());
                    return false;
                }
            break;
            case 'xpressbees': //Xpressbees 
                $this->CI->load->library('shipping/Xpressbees');
                $xb = new Xpressbees(array('mode' => $courier->code));
                if (!$awb = $xb->createOrder($shipment_data)) {
                    $this->error = $xb->get_error();
                    $this->updateShipmentMessage($shipment_id, $xb->get_error());
                    return false;
                }
            break;
              case 'daakit go':
                  $this->CI->load->library('shipping/DaakitGo');
                  $daakitgo = new DaakitGo();
                  if (!$awb = $daakitgo->createOrder($shipment_data)) {
                      $this->error = $daakitgo->get_error();
                      $this->updateShipmentMessage($shipment_id, $daakitgo->get_error());
                      return false;
                    }
             break;
            default:
                $this->updateShipmentMessage($shipment_id, 'Courier service is not recognized');
                return false;
        }

        if (empty($awb) || !array_key_exists($courier_shipment_id, $awb)) {
            $this->error = 'Unable to create shipment';
            $this->updateShipmentMessage($shipment_id, 'Unable to create Shipment');
            return false;
        }

        if ($this->getByAWB($awb[$courier_shipment_id]['awb'])) {
            $this->error = 'Duplicate AWB No: ' . $awb[$courier_shipment_id]['awb'];
            $this->updateShipmentMessage($shipment_id, $this->error);
            return false;
        }

        if ($awb[$courier_shipment_id]['status'] == 'error') {
            $this->error = $awb[$courier_shipment_id]['awb'];
            $this->updateShipmentMessage($shipment_id, $awb[$courier_shipment_id]['awb']);
            return false;
        }

        //update awb in db
        $update = array(
            'awb_number' => $awb[$courier_shipment_id]['awb'],
            'ship_status' => 'booked',
            'label' => !empty($awb[$courier_shipment_id]['label']) ? $awb[$courier_shipment_id]['label'] : '',
            'shipment_info_1' => !empty($awb[$courier_shipment_id]['shipment_info_1']) ? $awb[$courier_shipment_id]['shipment_info_1'] : '',
            'additional_tracking_info' => !empty($awb[$courier_shipment_id]['additional_tracking_info']) ? $awb[$courier_shipment_id]['additional_tracking_info'] : '',
            'amazon_shipment_id' => !empty($awb[$courier_shipment_id]['amazon_shipment_id']) ? $awb[$courier_shipment_id]['amazon_shipment_id'] : '',
            'message' => ''
        );
        // insert weight changes details
        if ($applied_flag) {
            $products_details_data = $this->CI->products_lib->insertWeightAppliedDetails($weight_applied_details);
        }

        if ($is_price_calculate) {
            if (empty($shipping_cost['total'])) {
                $this->error = 'Freight cost is unavailable for calculation';
                $this->updateShipmentMessage($shipment_id, $this->error);
                return false;
            }

            if (!empty($shipping_cost)) {
                $update['courier_fees'] = $shipping_cost['courier_charges'];
                $update['cod_fees'] = $shipping_cost['cod_charges'];
                $update['total_fees'] = $shipping_cost['total'];
                $update['zone'] = $shipping_cost['zone'];
                $update['calculated_weight'] = $shipping_cost['calculated_weight'];

                $update['base_freight'] = $shipping_cost['base_freight'];
                $update['base_rto_freight'] = $shipping_cost['base_rto_freight'];
                $update['base_add_weight_freight'] = $shipping_cost['base_add_weight_freight'];

                if ($shipping_cost['courier_charges'] > 0) {
                    $wallet = new Wallet_lib(array('user_id' => $order->user_id));
                    $wallet->setAmount($shipping_cost['courier_charges']);
                    $wallet->setTransactionType('debit');
                    $wallet->setNotes('Freight Charges');
                    $wallet->setRefID($shipment_id);
                    $wallet->setTxnFor('shipment');
                    $wallet->setTxnRef('freight');

                    //deduct wallet for fees
                    $wallet->creditDebitWallet();
                }

                if ($shipping_cost['cod_charges'] > 0) {
                    $wallet = new Wallet_lib(array('user_id' => $order->user_id));
                    $wallet->setAmount($shipping_cost['cod_charges']);
                    $wallet->setTransactionType('debit');
                    $wallet->setNotes('COD Charges');
                    $wallet->setRefID($shipment_id);
                    $wallet->setTxnFor('shipment');
                    $wallet->setTxnRef('cod');

                    //deduct wallet for fees
                    $wallet->creditDebitWallet();
                }

                if ($shipment->insurance_price > 0) {
                    $wallet = new Wallet_lib(array('user_id' => $order->user_id));
                    $wallet->setAmount($shipment->insurance_price);
                    $wallet->setTransactionType('debit');
                    $wallet->setNotes('Shipment insurance charges');
                    $wallet->setRefID($shipment_id);
                    $wallet->setTxnFor('shipment');
                    $wallet->setTxnRef('insurance');
                    $wallet->creditDebitWallet();

                    $this->CI->load->library('insurance_lib');
                    $this->CI->insurance_lib->createInsurance($shipment->id, $awb[$courier_shipment_id]['awb']);
                }
            }
        } else {
            $update['courier_fees'] = 0;
            $update['cod_fees'] = 0;
            $update['total_fees'] = 0;
            $update['zone'] = $shipping_cost['zone'];
            $update['calculated_weight'] = $shipping_cost['calculated_weight'];
            $update['base_freight'] = 0;
            $update['base_rto_freight'] = 0;
            $update['base_add_weight_freight'] = 0;
        }

        $this->update($shipment->id, $update);

        if ($is_price_calculate) {
            $this->CI->load->library('plans_lib');
            $actualLanding = $this->CI->plans_lib->getActualLandingByCourierId($courier->id);

            $save = [];
            $save['shipping_id'] = $shipment_id;
            $save['courier_id'] = $courier->id;
            $save['zone'] = $shipping_cost['zone'];
            foreach ($actualLanding as $actual_landing) {
                $type = strtolower($actual_landing->type);
                switch ($type) {
                    case 'fwd':
                        $save['courier_fees'] = !empty($actual_landing->id) ? $actual_landing->{str_replace('z', 'zone', $shipping_cost['zone'])} : '0';
                        $save['min_cod'] = !empty($actual_landing->min_cod) ? $actual_landing->min_cod : '0';
                        $save['cod_percent'] = !empty($actual_landing->cod_percent) ? $actual_landing->cod_percent : '0';
                        break;
                    case 'rto':
                        $save['rto_fees'] = !empty($actual_landing->id) ? $actual_landing->{str_replace('z', 'zone', $shipping_cost['zone'])} : '0';
                        break;
                    case 'weight':
                        $save['extra_weight_fees'] = !empty($actual_landing->id) ? $actual_landing->{str_replace('z', 'zone', $shipping_cost['zone'])} : '0';
                        break;
                    default:
                        break;
                }
            }

            $save['shipment_weight'] = !empty($awb[$courier_shipment_id]['shipment_weight']) ? $awb[$courier_shipment_id]['shipment_weight'] : '0';

            $this->saveActualLandingCharges($save);
        }

        $channel_name = "";
        if(isset($order->channel_id) && !empty($order->channel_id)) {
            $channel = $this->getChannelName($order->channel_id);
            if(!empty($channel)){
                $channel_name = !empty($channel->channel) ? $channel->channel : "";
                do_action('shipping.booked', $shipment->id, $channel_name);
            }
        }
        return $awb[$courier_shipment_id];
    }

    function processRTOShipment($shipment_id = false)
    {
        return false;

        if (!$shipment_id)
            return false;

        $shipment = $this->getByID($shipment_id);

        if (empty($shipment))
            return false;

        $this->CI->load->library('orders_lib');
        $order = $this->CI->orders_lib->getByID($shipment->order_id);


        //check if rto charges are already applied
       
        if ($shipment->rto_charges > 0)
            return false;


            if ((int)$shipment->base_rto_freight == 0)
            return false;



        $update = array(
            'cod_reverse_amount' => $shipment->cod_fees,
            'rto_charges' => $shipment->courier_fees,
            'rto_extra_weight_charges' => $shipment->extra_weight_charges,
            'rto_date' => time(),
        );

        $this->update($shipment->id, $update);

        //apply rto charges

        $this->CI->load->library('wallet_lib');

        $wallet = new Wallet_lib(array('user_id' => $order->user_id));
        $wallet->setAmount($shipment->courier_fees + (($shipment->extra_weight_charges > 0) ? $shipment->extra_weight_charges : 0));
        $wallet->setTransactionType('debit');
        $wallet->setNotes('RTO Freight Charges');
        $wallet->setRefID($shipment_id);
        $wallet->setTxnFor('shipment');
        $wallet->setTxnRef('rto_freight');

        //credit wallet for fees
        $wallet->creditDebitWallet();


        if ($shipment->cod_fees > 0) {
            //revert cod fees
            $wallet = new Wallet_lib(array('user_id' => $order->user_id));
            $wallet->setAmount($shipment->cod_fees);
            $wallet->setTransactionType('credit');
            $wallet->setNotes('COD Charge Reversed due to RTO');
            $wallet->setRefID($shipment_id);
            $wallet->setTxnFor('shipment');
            $wallet->setTxnRef('cod');

            //credit wallet for fees
            $wallet->creditDebitWallet();
        }

        return true;
    }

    function schedulePickup($user_id = false, $shipment_ids = false)
    {
        if (!$user_id || empty($shipment_ids)) {
            $this->error = 'Invalid Pickup Data';
            return false;
        }

        //get warehouse data
        $this->CI->load->library('warehouse_lib');
        $warehouses = $this->CI->warehouse_lib->getUserAllWarehouse($user_id);

        if (empty($warehouses)) {
            $this->error = 'Warehouse information is not available';
            return false;
        }

        $warehouse_array = array();
        foreach ($warehouses as $warehouse) {
            $warehouse_array[$warehouse->id] = $warehouse;
            if ($warehouse->is_default)
                $warehouse_array['default'] = $warehouse;
        }

        //get all shipments by shipment ids
        $shipments = $this->getByIDBulk($shipment_ids);
        if (empty($shipment_ids)) {
            $this->error = 'No Shipment Found';
            return false;
        }

        $courier_count = array();
        $actual_courier_count = array();
        foreach ($shipments as $shipment) {
            if (in_array($shipment->ship_status, array('booked', 'pending pickup'))){ 
                // courier tranfer to another courier---New
                if(!empty($shipment->actual_courier_id)){
                    $actual_courier_count[$shipment->actual_courier_id] =$shipment->courier_id;
                }
                $shipment->courier_id=!empty($shipment->actual_courier_id) ? $shipment->actual_courier_id : $shipment->courier_id;
                $courier_count[$shipment->courier_id][$shipment->warehouse_id][] = $shipment->id;
            }
        }
        if (empty($courier_count)) {
            $this->error = 'No Shipments Found For Pickup';
            return false;
        }

        $this->CI->load->library('pickups_lib');

        $pickup_id = '';
        foreach ($courier_count as $c_key => $c_ount) {
            foreach ($c_ount as $warehouse_key => $warehouse_value) {
                $pickup_id = $this->processPickup($c_key, count($courier_count[$c_key][$warehouse_key]), ($warehouse_key == '0') ? $warehouse_array['default'] : $warehouse_array[$warehouse_key], $courier_count[$c_key][$warehouse_key]);

                if (!$pickup_id)
                    return false;

                //mark shipments for this courier as pending pickup
                $this->markPickupRequested($courier_count[$c_key][$warehouse_key]);

                //save pickup info in DB
                $pickup_info = array(
                    'user_id' => $user_id,
                    'courier_id' => !empty($actual_courier_count[$c_key]) ? $actual_courier_count[$c_key] : $c_key,
                    'pickup_number' => $pickup_id,
                    //'shipment_ids' => implode(',', $courier_count[$c_key][$warehouse_key]),
                    'warehouse_id' => $warehouse_key,
                );

                $pickup_id = $this->CI->pickups_lib->insert($pickup_info);
              
                foreach ($courier_count[$c_key][$warehouse_key] as $shipment_id) {
                    $pickup_data = array(
                        'user_id' => $user_id,
                        'pickup_id' => $pickup_id,
                        'shipment_id' => $shipment_id
                    );

                    $match_pickup_id = $this->CI->pickups_lib->matchPickupData($pickup_data);

                    if(empty($match_pickup_id->id)) {
                        $this->CI->pickups_lib->insert_pickup_data($pickup_data);
                    }
                }
            }
        }

        return $pickup_id;
    }

    function getPickupDateTime()
    {
        $holiday_list = $this->CI->config->item('courier_holiday_list');

        $pickup_time = '19:00:00';
        $pickup_date = date('Y-m-d');

        $now = new Datetime('now');

        $time1 = new DateTime('08:00');
        $time2 = new DateTime('14:00');
        $time3 = new DateTime('23:59');
        $time4 = new DateTime('00:01');

        //condition 1  check if before monrning 8
        if ($now >= $time4 && $now <= $time1) {
            $pickup_time = '10:00:00';
        }

        if ($now >= $time1 && $now <= $time2)
            $pickup_time = '16:00:00';

        if ($now >= $time2 && $now <= $time3) {
            $pickup_date = date('Y-m-d', strtotime('+1 day'));
            $pickup_time = '10:00:00';
        }

        $pickup_day = date('N', strtotime($pickup_date)); //1 for monday, 7 for sunday

        while ($pickup_day == '7' || in_array($pickup_date, $holiday_list)) {
            $pickup_date = date('Y-m-d', strtotime($pickup_date . '+1 day'));
            $pickup_day = date('N', strtotime($pickup_date));
        }

        return array('pickup_date' => $pickup_date, 'pickup_time' => $pickup_time);
    }

    private function processPickup($courier_id = false, $packets = 1, $warehouse = false, $shipment_ids = false)
    {
        if (!$courier_id) {
            $this->error = 'Courier Partner not available';
            return false;
        }

        //get courier details
        $this->CI->load->library('courier_lib');
        $courier = $this->CI->courier_lib->getByID($courier_id);

        $pickup_date_time = $this->getPickupDateTime();

        $pickup_date = $pickup_date_time['pickup_date'];
        $pickup_time = $pickup_date_time['pickup_time'];

        $pickup_id = false;
        if(!empty($courier->aggregator_courier_id))
            $courier->display_name='Fship';

        switch (strtolower($courier->display_name)) {
            case 'fship': //fship
                $this->CI->load->library('shipping/aggregator/Fship');
                $fship = new Fship();
                $pickup_id = $fship->pickup($shipment_ids, $packets, $pickup_date, $pickup_time);
            break;
            case 'pickndel': //Pickndel
                $pickup_id='NA';
            break;
            case 'purpledrone': 
                $pickup_id='NA';
            break;
            case 'xpressbees': //Xpressbees
                $pickup_id='NA';
            break;
            case 'delhivery': //Delhivery 
                $pickup_id='NA';
                // $this->CI->load->library('shipping/Delhivery');
                // $delhivery = new Delhivery(array('mode' => $courier->code));
                // $pickup_id = $delhivery->pickup($packets, $pickup_date, $pickup_time, $warehouse->id);
            break;
            default:
                $this->error = 'Courier partner not available';
                return false;
        }

        if (!$pickup_id) {
            $this->error = 'Unable to place Pickup Request';
            return false;
        }

        do_action('courier.pickup_request', $courier_id);

        return $pickup_id;
    }

    function generateLabel($shipment_ids = false, $format = 'thermal', $user_id = false)
    {
        ini_set("pcre.backtrack_limit", "5000000");

        if (empty($shipment_ids))
            return false;

        if ($format == 'thermal') {
            $mpdf = new \Mpdf\Mpdf([
                'tempDir' => './temp',
                'mode' => 'utf-8',
                'format' => [101, 154],
                'margin_left' => 0,
                'margin_top' => 0,
                'margin_right' => 0,
                'margin_bottom' => 0,
            ]);
        } else {
            $mpdf = new \Mpdf\Mpdf([
                'tempDir' => './temp',
                'mode' => 'utf-8',
                'margin_left' => 5,
                'margin_top' => 5,
                'margin_right' => 5,
                'margin_bottom' => 5,
            ]);
        }

        $shipment_data = array();
        foreach ($shipment_ids as $shipment_id) {
            if ($order = $this->getShipmentData($shipment_id)) {
                if ($user_id && $order->shipment->user_id != $user_id)
                    continue;

                if (!in_array($order->shipment->ship_status, array('cancelled', 'new')))
                    $shipment_data[] = $order;
            }
        }

        if (empty($shipment_data))
            return false;

        $pdf_content = $this->CI->load->view('shipping/label', array('shipments' => $shipment_data, 'format' => $format), true);
        //pr($pdf_content,1);
        $mpdf->WriteHTML($pdf_content);

        $this->CI->load->library('s3');

        $directory = 'assets/labels/';
        $file_name = date('YmdHis') . '-' . $user_id . rand(1000, 9999)  . '.pdf';

        $mpdf->Output($directory . $file_name, 'F');
        $aws_file_name = $this->CI->s3->amazonS3Upload($file_name, $directory . $file_name, 'labels');

        //$mpdf->Output();
        //unlink($directory . $file_name);

        return $aws_file_name;
    }


    function generateLabelawb($awb_no = false, $format = 'thermal', $user_id = false)
    {
        if (empty($awb_no))
            return false;

        $awbrec_data = array();
        foreach ($awb_no as $awb_numb) {
            if ($order = $this->getShipmentDataAWB(trim($awb_numb))) {
                if ($user_id && $order->shipment->user_id != $user_id)
                    continue;

                if (!in_array($order->shipment->ship_status, array('cancelled', 'new'))){ 
                    $awbrec_data[] =array('orderNo'=>$order->order->order_no,
                        'orderDate'=>$order->order->order_date,
                        'paymentMode'=>strtolower($order->order->order_payment_type),
                        'orderAmount'=>$order->order->order_amount,
                        'awbNumber'=>$order->shipment->awb_number,
                        'awbStatus'=>$order->shipment->ship_status,
                        'shipmentWeight'=>($order->shipment->package_weight > 0) ? round($order->shipment->package_weight / 1000, 1) : '0.5',
                        'shipmentLength'=>$order->shipment->package_length??0,
                        'shipmentBreadth'=>$order->shipment->package_breadth??0,
                        'shipmentHeight'=>$order->shipment->package_height??0,
                        'courierName'=>$order->courier->name,
                        'courierDisplayName'=>$order->courier->display_name,
			"sellerName"=> $order->user->fname." ".$order->user->lname,
			"gst_number"=> $order->legal_entity->legal_gstno,
                        "shipmentExtraInfo"=> (strtolower($order->courier->display_name)=='bluedart') ? $order->shipment->shipment_info_1 : '',
                        'consigneeDetails'=>array("consigneeName"=> $order->order->shipping_fname." ".$order->order->shipping_lname,
                            "consigneeAddress"=> $order->order->shipping_address,
                            "consigneeAddress2"=>$order->order->shipping_address_2,
                            "consigneeCity"=> $order->order->shipping_city,
                            "consigneeState"=> $order->order->shipping_state,
                            "consigneePincode"=> $order->order->shipping_zip,
                            "consigneePhone"=> $order->order->shipping_phone
                        ),
                        'productDetails'=>self::getProductsKeyValue((array)$order->products),
                        'pickupWarehouseDetails'=>array("pickupWarehouseName"=> $order->warehouse->name,
                            "pickupName"=> $order->warehouse->contact_name,
                            "pickupAddress"=> $order->warehouse->address_1,
                            "pickupAddress2"=> $order->warehouse->address_2,
                            "pickupPincode"=> $order->warehouse->zip,
                            "pickupPhone"=> $order->warehouse->phone,
                            "pickGstNumber"=> $order->warehouse->gst_number,
                        ),
                        'rtoWarehouseDetails'=>array("rtoWarehouseName"=> $order->rto_warehouse->name,
                            "rtoName"=> $order->rto_warehouse->contact_name,
                            "rtoAddress"=> $order->rto_warehouse->address_1,
                            "rtoAddress2"=> $order->rto_warehouse->address_2,
                            "rtoPincode"=> $order->rto_warehouse->zip,
                            "rtoPhone"=> $order->rto_warehouse->phone,
                            "rtoGstNumber"=> $order->rto_warehouse->gst_number,
                        )
                    );
                }
            }
        }

        if (empty($awbrec_data))
            return false;

        return $awbrec_data;
    }

    function getProductsKeyValue($productDetails)
    {
        if(empty($productDetails))
            return array();

        foreach ($productDetails as &$item) {
            if (!is_array($item)) {
                $item = (array) $item; // Cast it to an array if it isn't one
            }
            foreach ($item as $key => $value) {
                if (strpos($key, '_')) {
                    list($first,$second) = explode('_',$key); // Remove '_'
                    $item[$first.ucfirst($second)] = $value;
                    unset($item[$key]); // Remove old key
                    unset($item['orderId']); // Remove old key
                    unset($item['productId']); // Remove old key
                    unset($item['userId']); // Remove old key
                    unset($item['productWeight']); // Remove old key
                }
                else{
                    unset($item[$key]);
                }
            }
        }   
        return $productDetails;
    }

    function getShipmentDataAWB($awb_no = false)
    {
        if (!$awb_no)
            return false;

        if (!$shipment = $this->getByAWB($awb_no))
            return false;

        $this->CI->load->library('pincode_lib');

        $this->CI->load->library('orders_lib');
        $order = $this->CI->orders_lib->getByID($shipment->order_id);

        //get products
        $products = $this->CI->orders_lib->getOrderProducts($order->id);

        //get courier data
        $this->CI->load->library('courier_lib');
        $courier = $this->CI->courier_lib->getByID($shipment->courier_id);

        //get warehouse details
        $this->CI->load->library('warehouse_lib');

        if ($shipment->warehouse_id != '0') {
            $warehouse = $this->CI->warehouse_lib->getByID($shipment->warehouse_id);
        } else {
            $warehouse = $this->CI->warehouse_lib->getUserWarehouse($order->user_id);
        }

        $rto_warehouse = array();
        $is_rto_different = false;

        if ($shipment->rto_warehouse_id == '0' || $shipment->rto_warehouse_id == $shipment->warehouse_id) {
            $rto_warehouse = $warehouse;
            $is_rto_different = false;
        } else {
            $rto_warehouse = $this->CI->warehouse_lib->getByID($shipment->rto_warehouse_id);
            $is_rto_different = true;
        }

        $shipment->is_rto_different = $is_rto_different;

        //get company details
        $this->CI->load->library('profile_lib');
	$company = $this->CI->profile_lib->getCompanyByUserID($order->user_id);
	$legal_entity = $this->CI->profile_lib->getLegalDetailsByUserId($order->user_id);

        $this->CI->load->library('user_lib');
        $user = $this->CI->user_lib->getByID($order->user_id);

        //add area code to warehouse

        $pickup_pincode_details = $this->CI->pincode_lib->checkPickupServiceByCourier($warehouse->zip, $courier->id);

        if (!empty($pickup_pincode_details)) {
            $warehouse->area_code = $pickup_pincode_details->area_code;
        }

        $delivery_pincode_details = $this->CI->pincode_lib->checkPincodeServiceByCourier($order->shipping_zip, $courier->id, $order->order_payment_type);

        if (!empty($delivery_pincode_details)) {
            $courier->delivery_area_code = $delivery_pincode_details->area_code;
        }

        if (empty($order->package_weight) || !(floor($order->package_weight / 500) >= 1)) {
            $order->package_weight = 500;
        }

        if (empty($order->package_length)) {
            $order->package_length = '10';
        }

        if (empty($order->package_height)) {
            $order->package_height = '10';
        }

        if (empty($order->package_breadth)) {
            $order->package_breadth = '10';
        }

        $return = array(
            'order' => $order,
            'products' => $products,
            'shipment' => $shipment,
            'courier' => $courier,
            'warehouse' => $warehouse,
            'rto_warehouse' => $rto_warehouse,
            'company' => $company,
	    'user' => $user,
	    'legal_entity' => $legal_entity
        );

        return (object) $return;
    }

    function getShipmentData($shipment_id = false)
    {
        if (!$shipment_id)
            return false;

        if (!$shipment = $this->getByID($shipment_id))
            return false;

        $this->CI->load->library('pincode_lib');

        $this->CI->load->library('orders_lib');
        $order = $this->CI->orders_lib->getByID($shipment->order_id);

        //get products
        $products = $this->CI->orders_lib->getOrderProducts($order->id);

        //get courier data
        $this->CI->load->library('courier_lib');
        $courier = $this->CI->courier_lib->getByID($shipment->courier_id);

        //get warehouse details
        $this->CI->load->library('warehouse_lib');

        if ($shipment->warehouse_id != '0') {
            $warehouse = $this->CI->warehouse_lib->getByID($shipment->warehouse_id);
        } else {
            $warehouse = $this->CI->warehouse_lib->getUserWarehouse($order->user_id);
        }

        $rto_warehouse = array();
        $is_rto_different = false;

        if ($shipment->rto_warehouse_id == '0' || $shipment->rto_warehouse_id == $shipment->warehouse_id) {
            $rto_warehouse = $warehouse;
            $is_rto_different = false;
        } else {
            $rto_warehouse = $this->CI->warehouse_lib->getByID($shipment->rto_warehouse_id);
            $is_rto_different = true;
        }

        $shipment->is_rto_different = $is_rto_different;

        //get company details
        $this->CI->load->library('profile_lib');
        $company = $this->CI->profile_lib->getCompanyByUserID($order->user_id);

        $this->CI->load->library('user_lib');
        $user = $this->CI->user_lib->getByID($order->user_id);

        //add area code to warehouse

        $pickup_pincode_details = $this->CI->pincode_lib->checkPickupServiceByCourier($warehouse->zip, $courier->id);

        if (!empty($pincode_details)) {
            $warehouse->area_code = $pickup_pincode_details->area_code;
        }

        //add pincode code to courier

        $delivery_pincode_details = $this->CI->pincode_lib->checkPincodeServiceByCourier($order->shipping_zip, $courier->id, $order->order_payment_type);

        if (!empty($delivery_pincode_details)) {
            $courier->delivery_area_code = $delivery_pincode_details->area_code;
        }

        if (empty($order->package_weight) || !(floor($order->package_weight / 500) >= 1)) {
            $order->package_weight = 500;
        }

        if (empty($order->package_length)) {
            $order->package_length = '10';
        }

        if (empty($order->package_height)) {
            $order->package_height = '10';
        }

        if (empty($order->package_breadth)) {
            $order->package_breadth = '10';
        }

        $channel_brand_logo = '';
        if (!empty($order->channel_id)) {
            $channel_brand_logo = array();
            $channel_brands_logo = $this->CI->profile_lib->get_channel_data($order->channel_id);
            if (!empty($channel_brands_logo)) {
                $channel_brand_logo['channel_brand_logo'] = !empty($channel_brands_logo->brand_logo) ? $channel_brands_logo->brand_logo : '';
            }
        }

        $return = array(
            'order' => $order,
            'products' => $products,
            'shipment' => $shipment,
            'courier' => $courier,
            'warehouse' => $warehouse,
            'rto_warehouse' => $rto_warehouse,
            'company' => $company,
            'user' => $user,
            'channels_brand_logo' => (object)$channel_brand_logo
        );

        return (object) $return;
    }

    function getTrackingHistoryLive($shipment_id = false, $rto_tracking = false)
    {
        if (!$shipment_id)
            return false;

        //get shipping details for this order
        $shipment = $this->getByID($shipment_id);

        if (empty($shipment)) {
            $this->error = 'Shipment Not Found';
            return false;
        }
        // courier tranfer to another courier---New
        $shipment->courier_id=!empty($shipment->actual_courier_id) ? $shipment->actual_courier_id : $shipment->courier_id;
        $this->update($shipment_id, array('last_tracking_time' => time()));

        $awb_number = false;

        if ($rto_tracking && !empty($shipment->rto_awb)) {
            $awb_number = $shipment->rto_awb;
        } else {
            $awb_number = $shipment->awb_number;
            $rto_tracking = false;
        }

        if (empty($awb_number))
            return false;

        if ($shipment->ship_status == 'cancelled')
            return false;

        //get tracking history from courier API
        //get courier data
        $this->CI->load->library('courier_lib');
        $courier = $this->CI->courier_lib->getByID($shipment->courier_id);

        $clear_db = false;
        if(!empty($courier->aggregator_courier_id))
            $courier->display_name='Fship';

        switch (strtolower($courier->display_name)) {
                case 'fship': //fship
                    $this->CI->load->library('shipping/aggregator/Fship');
                    $fship = new Fship();
                    $history = $fship->trackOrder($awb_number);
                break;
                case 'pickndel': //Pickndel
                    $this->CI->load->library('shipping/Pickndel');
                    $pickndel = new Pickndel();
                    $history = $pickndel->trackOrder($awb_number);
                break;
                case 'purpledrone': //Purpledrone
                    $this->CI->load->library('shipping/Purpledrone');
                    $purpledrone = new Purpledrone();
                    $history = $purpledrone->trackOrder($awb_number);
                break;
                case 'delhivery': //Delhivery 
                    $this->CI->load->library('shipping/Delhivery');
                    $delhivery = new Delhivery(array('mode' => $courier->code));
                    $history = $delhivery->trackOrder($awb_number);
                    $clear_db = true;
                break;
                case 'xpressbees': //XpressBees
                    $this->CI->load->library('shipping/Xpressbees');
                    $xb = new Xpressbees(array('mode' => $courier->code));
                    $history = $xb->trackOrder($awb_number);
                    $clear_db = true;
                break;
            default:  
                $this->error = 'Courier partner not available';
                return false;
        }

        if (empty($history[$awb_number]))
            return false;

        $this->CI->load->helper('tracking');

        $history[$awb_number] = modifiedTrackingHistory($history[$awb_number], $courier);

        //update tracking history in db
        $save = array();
        foreach ($history[$awb_number]['history'] as $awb_history) {
            if (!empty($awb_history['event_time']))
                if ($rto_tracking) {
                    $ship_status = (in_array($awb_history['ship_status'], ['delivered', 'rto delivered'])) ? 'rto delivered' : 'rto in transit';
                } else {
                    $ship_status = $awb_history['ship_status'];
                }
            $save[] = array(
                'awb_number' => $awb_number,
                'event_time' => $awb_history['event_time'],
                'status_code' => $awb_history['status_code'],
                'location' => $awb_history['location'],
                'message' => $awb_history['message'],
                'status' => $awb_history['status'],
                'ship_status' => !empty($ship_status) ? $ship_status : '',
                'rto_awb' => !empty($awb_history['rto_awb']) ? $awb_history['rto_awb'] : '',
            );
        }

        array_multisort(array_column($save, 'event_time'), SORT_DESC, $save);
        $this->CI->load->library('tracking_lib');

        if (empty($save))
            return false;

        $zone = !empty($shipment->zone) ? $shipment->zone : '';
        $courier_type = !empty($courier->courier_type) ? $courier->courier_type : '';

        $courier_edd_days = $this->CI->config->item('courier_edd_days');

        $edd_time = (!empty($history[$awb_number]['pickup_time']) && !empty($courier_edd_days[$zone][$courier_type])) ? ($history[$awb_number]['pickup_time'] + ($courier_edd_days[$zone][$courier_type] * 86400)) : '';

        if ($clear_db)
            $this->CI->tracking_lib->deleteByAWB($awb_number);
        else {
            //delete existing events for this AWB
            $this->CI->tracking_lib->deleteByAWBEventTime($awb_number, $save['0']['event_time']);
        } 
        //insert fresh data in the table
        if (!empty($save))
            $this->CI->tracking_lib->batchInsert($save);

        //update tracking info in shipment data in the table
        if (!empty($history[$awb_number]['additional_tracking_info'])) {
            $this->update($shipment_id, array('additional_tracking_info' => $history[$awb_number]['additional_tracking_info']));
        }

        //update edd time in shipment data in the table
        if (!empty($edd_time)) {
            $this->update($shipment_id, array('edd_time' => $edd_time));
        }

        if (in_array($shipment->ship_status, array('cancelled', 'delivered', 'lost', 'damaged'))) {
            return false;
        }

        if (!$rto_tracking)
            $save_shipment_tracking = array(
                'shipment_id' => $shipment->id,
                'pickup_time' => (!empty($history[$awb_number]['pickup_time'])) ? $history[$awb_number]['pickup_time'] : '',
                'edd_time' => $edd_time,
                'delivered_time' => (!empty($history[$awb_number]['delivered_time'])) ? $history[$awb_number]['delivered_time'] : '',
                'weight' => (!empty($history[$awb_number]['weight'])) ? $history[$awb_number]['weight'] : 0,
                'shipment_status' => (!empty($history[$awb_number]['shipment_status'])) ? $history[$awb_number]['shipment_status'] : '',
                'expected_delivery_date' => (!empty($history[$awb_number]['expected_delivery_date'])) ? $history[$awb_number]['expected_delivery_date'] : '',
                'reached_at_destination_hub' => (!empty($history[$awb_number]['reached_at_destination_hub'])) ? $history[$awb_number]['reached_at_destination_hub'] : '',
                'ofd_attempt_1_date' => (!empty($history[$awb_number]['ofd_attempt_1_date'])) ? $history[$awb_number]['ofd_attempt_1_date'] : '',
                'ofd_attempt_2_date' => (!empty($history[$awb_number]['ofd_attempt_2_date'])) ? $history[$awb_number]['ofd_attempt_2_date'] : '',
                'ofd_attempt_3_date' => (!empty($history[$awb_number]['ofd_attempt_3_date'])) ? $history[$awb_number]['ofd_attempt_3_date'] : '',
                'last_attempt_date' => (!empty($history[$awb_number]['last_attempt_date'])) ? $history[$awb_number]['last_attempt_date'] : '',
                'total_ofd_attempts' => (!empty($history[$awb_number]['total_ofd_attempts'])) ? $history[$awb_number]['total_ofd_attempts'] : 0,
                'rto_mark_date' => (!empty($history[$awb_number]['rto_mark_date'])) ? $history[$awb_number]['rto_mark_date'] : 0,
                'rto_delivered_date' => (!empty($history[$awb_number]['rto_delivered_date'])) ? $history[$awb_number]['rto_delivered_date'] : 0,
                'last_ndr_reason' => (!empty($history[$awb_number]['last_ndr_reason'])) ? $history[$awb_number]['last_ndr_reason'] : 0,
                'last_ndr_date' => (!empty($history[$awb_number]['last_ndr_date'])) ? $history[$awb_number]['last_ndr_date'] : 0,
                'first_pickup_attempt' => (!empty($history[$awb_number]['first_pickup_attempt'])) ? $history[$awb_number]['first_pickup_attempt'] : '0',
                'last_pickup_attempt' => (!empty($history[$awb_number]['last_pickup_attempt'])) ? $history[$awb_number]['last_pickup_attempt'] : '0',
                'pickup_attempt_count' => (!empty($history[$awb_number]['pickup_attempt_count'])) ? $history[$awb_number]['pickup_attempt_count'] : '0',
            );

        if (!empty($history[$awb_number]['delivery_attempt_count'])) {
            $save_shipment_tracking['delivery_attempt_count'] = $history[$awb_number]['delivery_attempt_count'];
        }

        if (!empty($history[$awb_number]['picked_date'])) {
            $save_shipment_tracking['picked_date'] = $history[$awb_number]['picked_date'];
        }

        if (!empty($history[$awb_number]['shipped_date'])) {
            $save_shipment_tracking['shipped_date'] = $history[$awb_number]['shipped_date'];
        }

        if (!empty($history[$awb_number]['otp_verified'])) {
            $save_shipment_tracking['otp_verified'] = $history[$awb_number]['otp_verified'];
        }

        if (!empty($history[$awb_number]['otp_base_delivery'])) {
            $save_shipment_tracking['otp_base_delivery'] = $history[$awb_number]['otp_base_delivery'];
        }

        if (!empty($history[$awb_number]['otp_verified_cancelled'])) {
            $save_shipment_tracking['otp_verified_cancelled'] = $history[$awb_number]['otp_verified_cancelled'];
        }

        if (!empty($history[$awb_number]['ivr_verified_cancelled'])) {
            $save_shipment_tracking['ivr_verified_cancelled'] = $history[$awb_number]['ivr_verified_cancelled'];
        }

        if (!empty($courier->display_name) && $courier->display_name=='Smartr' && strtolower($shipment->payment_type)=='prepaid') {
            $save_shipment_tracking['otp_base_delivery'] = '1';
        }
        
        if ($rto_tracking) {
            $save_shipment_tracking = array(
                'shipment_id' => $shipment->id,
                'rto_mark_date' => (!empty($history[$awb_number]['rto_mark_date'])) ? $history[$awb_number]['rto_mark_date'] : ((!empty($history[$awb_number]['pickup_time'])) ? $history[$awb_number]['pickup_time'] : 0),
                'rto_delivered_date' => (!empty($history[$awb_number]['rto_delivered_date'])) ? $history[$awb_number]['rto_delivered_date'] : ((!empty($history[$awb_number]['delivered_time'])) ? $history[$awb_number]['delivered_time'] : 0),
            );
        }
        $this->CI->tracking_lib->createUpdateShipmentTracking($shipment->id, $save_shipment_tracking);

        do_action('shipping.status', $shipment->id, $save[0]);
        $send_status = !empty($save[0]['ship_status']) ? $save[0]['ship_status'] : '';
        $this->CI->load->library('notification_lib');

       $response = $this->CI->notification_lib->sendNotification($shipment->id, $send_status, null);

        return true;
    }

    function getTrackingData($awb_number = false, $is_rto = false, $user_id = false)
    {
        //get data for tracking
        if (!$awb_number)
            return false;

        $shipment = false;

        // Check for MPS number
        $this->CI->db->select('awb_number');
        $this->CI->db->where('mps_number', $awb_number);
        $q = $this->CI->db->get('order_mps');
        $mps_number = $q->row();

        if (!empty($mps_number)) {
            $awb_number = $mps_number->awb_number;
        }

        if ($is_rto)
            $shipment = $this->getByRtoAWB($awb_number, $user_id);
        else
            $shipment = $this->getByAWB($awb_number, $user_id);

        if (empty($shipment))
            return false;

        $this->CI->load->library('orders_lib');
        $order = $this->CI->orders_lib->getByID($shipment->order_id);

        if (empty($order))
            return false;

        //get courier data
        $this->CI->load->library('courier_lib');
        $courier = $this->CI->courier_lib->getByID($shipment->courier_id);

        //get tracking data
        $this->CI->load->library('tracking_lib');
        $tracking = $this->CI->tracking_lib->getByAWB($awb_number);

        //get company details
        $this->CI->load->library('profile_lib');
        $company = $this->CI->profile_lib->getCompanyByUserID($shipment->user_id);

        //get product details
        $this->CI->load->library('products_lib');
        $products = $this->CI->products_lib->getProductByOrderId($shipment->order_id);

        //get user details
        $this->CI->load->library('user_lib');
        $user = $this->CI->profile_lib->getByID($shipment->user_id);


        $return = array(
            'order' => $order,
            'shipment' => $shipment,
            'courier' => $courier,
            'tracking' => $tracking,
            'company' => $company,
            'user' => $user,
            'awb_number' => $awb_number,
            'user_tracking_setting' => '',
            'products' => $products
        );

        return (object) $return;
    }

    function markChannelOrderFulfill($shipment_id = false)
    {

        if (!$shipment_id)
            return false;

        $shipment = $this->getByID($shipment_id);

        if (empty($shipment) || $shipment->channel_fulfilled == '1')
            return false;

        if (in_array($shipment->ship_status, ['new', 'cancelled']))
            return false;

        $this->CI->load->library('orders_lib');
        $order = $this->CI->orders_lib->getByID($shipment->order_id);

        if (empty($order)) {
            $update = array(
                'fulfillment_error' => 'order not found',
                'channel_fulfilled' => '2',
            );

            $this->update($shipment_id, $update);
            return false;
        }


        $this->CI->load->library('courier_lib');
        $courier = $this->CI->courier_lib->getByID($shipment->courier_id);

        if (empty($order->channel_id)) {
            $update = array(
                'fulfillment_error' => 'channel not found',
                'channel_fulfilled' => '2',
            );

            $this->update($shipment_id, $update);
            return false;
        }

        $this->CI->load->library('channels_lib');
        $channel = $this->CI->channels_lib->getByID($order->channel_id);


        if (empty($channel->channel)) {
            $update = array(
                'fulfillment_error' => 'channel not found',
                'channel_fulfilled' => '2',
            );

            $this->update($shipment_id, $update);
            return false;
        }

        $channel_name   = trim($channel->channel);
        $autofill       = array('1', '2', '3', '4');
        if (empty($channel) || !in_array($channel->auto_fulfill, $autofill)) {
            $update = array(
                'fulfillment_error' => 'Custom Order or Fulfillment Disabled',
                'channel_fulfilled' => '2',
            );

            $this->update($shipment_id, $update);
            return false;
        }

        $this->CI->load->library('apps/aftership_lib');
        $aftership_data = $this->CI->aftership_lib->getByUserID($shipment->user_id);

        $trk_subdomain = false;
        if (!empty($aftership_data)) {
            if (!empty($aftership_data->subdomain)) {
                $trk_subdomain = strtolower($aftership_data->subdomain);
            }
        }

        $fulfilled = false;
        $fulfilled_error = false;

        if ($channel->last_fulfillment_time >= time()) {
            //reassign to queue for later processing. 
            do_action('shipping.booked', $shipment->id, $channel->channel);
            return false;
        }

        if ($channel_name != 'shopify')
            $this->CI->channels_lib->update($channel->id, ['last_fulfillment_time' => time()]);
     
        switch (strtolower($channel->channel)) {
            case 'shopify':
                $config = array(
                    'channel_id' => $channel->id
                );

                $shopify_mark_auto_fulfill = false;
                switch ($channel->auto_fulfill) {
                    case 1:
                        if ($channel->auto_fulfill == 1 &&  (strtolower($shipment->ship_status) == 'booked' || strtolower($shipment->ship_status) == 'pending pickup') || strtolower($shipment->ship_status) == 'in transit' || strtolower($shipment->ship_status) == 'out for delivery' || strtolower($shipment->ship_status) == 'delivered')
                            $shopify_mark_auto_fulfill = true;
                        break;

                    case 3:
                        if ($channel->auto_fulfill == 3 &&  strtolower($shipment->ship_status) == 'in transit' || strtolower($shipment->ship_status) == 'out for delivery' || strtolower($shipment->ship_status) == 'delivered')
                            $shopify_mark_auto_fulfill = true;
                        break;

                    case 4:
                        if ($channel->auto_fulfill == 4 &&    strtolower($shipment->ship_status) == 'out for delivery' || strtolower($shipment->ship_status) == 'delivered')
                            $shopify_mark_auto_fulfill = true;
                        break;

                    case 2:
                        if ($channel->auto_fulfill == 2  &&  strtolower($shipment->ship_status) == 'delivered')
                            $shopify_mark_auto_fulfill = true;
                        break;
                }

                if ($shopify_mark_auto_fulfill) {
                    $this->CI->channels_lib->update($channel->id, ['last_fulfillment_time' => time()]);

                    $load_name = 'shopify_' . $channel->id;
                    $this->CI->load->library('channels/shopify', $config, $load_name);

                    if (!$fulfilled = $this->CI->{$load_name}->fulfill_order($order->api_order_id, ucwords($courier->display_name), $shipment->awb_number, $trk_subdomain)) {
                        $fulfilled_error = $this->CI->{$load_name}->get_error();
                    }
                } else {
                    return false; // if not shopify_mark_auto_fulfill  
                }
                break;  
            default:
                return false;
        }

        if ($fulfilled) {
            $update = array(
                'fulfillment_error' => 'fulfilled',
                'channel_fulfilled' => '1',
            );
        } else {
            $update = array(
                'fulfillment_error' => $fulfilled_error,
                'channel_fulfilled' => '2',
            );
        }
        $this->update($shipment_id, $update);

        return true;
    }

    function cancelShipment($shipment_id = false, $user_id = false)
    {
        if (!$shipment_id)
            return false;

        $shipment = $this->getByID($shipment_id);

        if (empty($shipment)) {
            $this->error = 'Shipment not available';
            return false;
        }

        $this->CI->load->library('orders_lib');
        $order = $this->CI->orders_lib->getByID($shipment->order_id);

        if (empty($order) || $order->user_id != $user_id) {
            $this->error = 'No Records Found';
            return false;
        }

        if (!in_array(strtolower($shipment->ship_status), array('new', 'booked', 'pending pickup'))) {
            $this->error = 'Unable to cancel';
            return false;
        }

        $update = array(
            'ship_status' => 'cancelled'
        );

        $this->update($shipment_id, $update); //update shipment

        //update order
        $update_order = array(
            'fulfillment_status' => 'new',
        );

        $this->CI->orders_lib->update($order->id, $update_order);

        $this->CI->load->library('wallet_lib');
        //refund the courier fees
        if ($shipment->courier_fees > 0 && !empty($shipment->awb_number)) {
            $wallet = new Wallet_lib(array('user_id' => $order->user_id));
            $wallet->setAmount($shipment->courier_fees);
            $wallet->setTransactionType('credit');
            $wallet->setNotes('Freight charges reversed for cancelled shipment');
            $wallet->setRefID($shipment_id);
            $wallet->setTxnFor('shipment');
            $wallet->setTxnRef('freight');
            //revert the fees
            $wallet->creditDebitWallet();
            $this->CI->shipping_lib->update($shipment_id, array('fees_refunded' => '1'));
        }

        //refund the COD fees
        if ($shipment->cod_fees > 0 && !empty($shipment->awb_number)) {
            $wallet = new Wallet_lib(array('user_id' => $order->user_id));
            $wallet->setAmount($shipment->cod_fees);
            $wallet->setTransactionType('credit');
            $wallet->setNotes('COD charges reversed for cancelled shipment');
            $wallet->setRefID($shipment_id);
            $wallet->setTxnFor('shipment');
            $wallet->setTxnRef('cod');
            //revert the fees
            $wallet->creditDebitWallet();
        }

        if ($shipment->insurance_price > 0 && !empty($shipment->awb_number)) {
            $wallet = new Wallet_lib(array('user_id' => $order->user_id));
            $wallet->setAmount($shipment->insurance_price);
            $wallet->setTransactionType('credit');
            $wallet->setNotes('Insurance charges reversed for cancelled shipment');
            $wallet->setRefID($shipment_id);
            $wallet->setTxnFor('shipment');
            $wallet->setTxnRef('insurance');
            $wallet->creditDebitWallet();
        }


        do_action('shipping.cancelled', $shipment->id);

        return true;
    }

    function cancelShipmentAtcourier($shipment_id = false)
    {
        if (!$shipment_id) {
            $this->error = 'Invalid ID';
            return false;
        }

        if (!$shipment = $this->getByID($shipment_id)) {
            $this->error = 'Invalid Shipment';
            return false;
        }
        // courier tranfer to another courier---New
        $shipment->courier_id=!empty($shipment->actual_courier_id) ? $shipment->actual_courier_id : $shipment->courier_id;

        if ($shipment->ship_status != 'cancelled') {
            $this->error = 'Shipment is not cancelled';
            return false;
        }

        $awb_number = $shipment->awb_number;

        if (empty($awb_number))
            return false;

        //get courier data
        $this->CI->load->library('courier_lib');
        $courier = $this->CI->courier_lib->getByID($shipment->courier_id);

        $clear_db = false;
        if(!empty($courier->aggregator_courier_id))
            $courier->display_name='Fship';

        switch (strtolower($courier->display_name)) {
                case 'fship': //fship
                    $this->CI->load->library('shipping/aggregator/Fship');
                    $fship = new Fship();
                    $fship->cancelAWB($awb_number);
                break;
                case 'pickndel': //Pickndel
                    $this->CI->load->library('shipping/Pickndel');
                    $pickndel = new Pickndel();
                    $pickndel->cancelAWB($awb_number,$shipment_id);
                break;
                case 'purpledrone': //Purpledrone
                    $this->CI->load->library('shipping/Purpledrone');
                    $purpledrone = new Purpledrone();
                    $purpledrone->cancelAWB($awb_number,$shipment_id);
                break;
                case 'delhivery': //Delhivery 
                    $this->CI->load->library('shipping/Delhivery');
                    $delhivery = new Delhivery(array('mode' => $courier->code));
                    $delhivery->cancelAWB($awb_number);
                break;
                case 'xpressbees': //XpressBees surface
                    $this->CI->load->library('shipping/Xpressbees');
                    $xb = new Xpressbees(array('mode' => $courier->code));
                    $xb->cancelAWB($awb_number);
                break;
                case 'daakit go':
                     $this->CI->load->library('shipping/DaakitGo');
                     $daakitgo = new DaakitGo();
                    $daakitgo->cancelAWB($awb_number);
                    break;
        }
        return true;
    }

    function savePickupTime($shipment_id = false)
    {
        if (!$shipment_id)
            return false;

        $shipment = $this->getByID($shipment_id);

        //no shipment
        if (!$shipment)
            return false;

        //shipment is already in transit
        if (in_array($shipment->ship_status, array('in transit')))
            return false;

        //shipment status is not booked or pending pickup
        if (!in_array($shipment->ship_status, array('booked', 'pending pickup')))
            return false;

        //shipment have pickup time 
        if (!empty($shipment->pickup_time))
            return false;

        $update = array(
            'pickup_time' => time(),
        );

        $this->update($shipment_id, $update);

        return true;
    }

    function saveDeliveredTime($shipment_id = false)
    {
        if (!$shipment_id)
            return false;

        $shipment = $this->getByID($shipment_id);

        //no shipment
        if (!$shipment)
            return false;

        //shipment is already delivered
        if (in_array($shipment->ship_status, array('delivered')))
            return false;

        //shipment have delivered time
        if (!empty($shipment->delivered_time))
            return false;

        $update = array(
            'delivered_time' => time(),
        );

        $this->update($shipment_id, $update);

        return true;
    }

    function getAPIShipments($filters = array())
    {
        $return = array();
        $shipments = $this->fetchAPIShipments($filters);
        if (!empty($shipments)) {
            foreach ($shipments as $shipment) {
                $return[] = array(
                    'id' => $shipment->id,
                    'orderId' => $shipment->order_id,
                    'orderNumber' => $shipment->order_number,
                    'created' => date('Y-m-d', $shipment->created),
                    'edd' => (!empty($shipment->ship_edd_time)) ? date('Y-m-d', $shipment->ship_edd_time) : '',
                    'pickupDate' => (!empty($shipment->ship_pickup_time)) ? date('Y-m-d', $shipment->ship_pickup_time) : (!empty($shipment->ship_pickup_time_alt) ? date('Y-m-d', $shipment->ship_pickup_time_alt) : ''),
                    'rtoInitiateDate' => (!empty($shipment->ship_rto_mark_date)) ? date('Y-m-d', $shipment->ship_rto_mark_date) : '',
                    'deliveredDate' => (!empty($shipment->ship_delivered_time)) ? date('Y-m-d', $shipment->ship_delivered_time) : '',
                    'shippedDate' => (!empty($shipment->ship_shipped_date)) ? date('Y-m-d', $shipment->ship_shipped_date) : '',
                    'awbNumber' => $shipment->awb_number,
                    'rtoAwb' => $shipment->rto_awb,
                    'courierId' => $shipment->courier_id,
                    'warehouseId' => $shipment->warehouse_id,
                    'rtoWarehouseId' => $shipment->rto_warehouse_id,
                    'status' => $shipment->ship_status,
                    'rtoStatus' => $shipment->rto_status,
		    'shipmentInfo' => $shipment->shipment_info_1,
		    'ndr_reason' => (!empty($shipment->last_ndr_reason)) ? $shipment->last_ndr_reason : '',
                );
            }
        }
        return $return;
    }

    function routeProcessingShipment($shipment_id = false, $is_api_shipment = false)
    {
        if (!$shipment_id)
            return false;

        $record = $this->getProcessingShipmentsByID($shipment_id);

        if (empty($record))
            return false;

        if ($record->ship_status != 'new' || !empty($record->awb_number))
            return false;

        $this->CI->load->library('user_lib');
        $user = $this->CI->user_lib->getByID($record->user_id);

        $this->CI->load->library('plans_lib');
        $this->CI->load->library('orders_lib');

        $this->CI->load->library('warehouse_lib');
        $warehouse = $this->CI->warehouse_lib->getByID($record->warehouse_id);

        if ($custom_plan = $this->CI->plans_lib->getCustomPlanByName($user->pricing_plan)) {
            return $this->routeProcessingShipmentForCustomPlan($shipment_id, $is_api_shipment);
        }

        $products = $this->CI->orders_lib->getOrderProductsGrouped($record->id);

        $allocation = new Allocation_lib($is_api_shipment);

        $allocation->setOrderDetails($record);
        
        $allocation->setUserID($record->user_id);

        $skip_couriers = array();

        if (!empty($record->allocation_skip_courier)) {
            $s_cs = explode(',', $record->allocation_skip_courier);
            $skip_couriers = array_merge($skip_couriers, $s_cs);
        }
        $skip_couriers[] = $record->courier_id;

        foreach ($skip_couriers as $skip)
            $allocation->setSkipCourier($skip);

        $allocation->setProductName(!empty($products->product_name) ? $products->product_name : '');
        $allocation->setProductSKU(!empty($products->product_sku) ? $products->product_sku : '');

        $allocation->setPaymentMode($record->order_payment_type);
        $allocation->setOrderAmount($record->order_amount);

        $allocation->setPickupPincode($warehouse->zip);
        $allocation->setDeliveryPincode($record->shipping_zip);

        $allocation->setWeight($record->package_weight);
        $allocation->setLength($record->package_length);
        $allocation->setBreadth($record->package_breadth);
        $allocation->setHeight($record->package_height);

        $allocation->setOrderSource($record->order_source);

        $allocation->setDangersGoodsFlag($record->dg_order);

        $rule_courier_id = $allocation->getRuleBasedCourier();

        if (!$rule_courier_id)
            return false;

        $update = array(
            'courier_id' => $rule_courier_id,
            'allocation_skip_courier' => implode(',', $skip_couriers),
        );

        $this->update($record->shipment_id, $update);

        if ($is_api_shipment) {
            return $rule_courier_id;
        }

        do_action('shipping.new', $record->shipment_id, $rule_courier_id);

        return true;
    }

    function routeProcessingShipmentForCustomPlan($shipment_id = false, $is_api_shipment = false)
    {
        if (!$shipment_id)
            return false;

        $record = $this->getProcessingShipmentsByID($shipment_id);

        if (empty($record))
            return false;

        if ($record->ship_status != 'new' || !empty($record->awb_number))
            return false;

        $this->CI->load->library('user_lib');
        $user = $this->CI->user_lib->getByID($record->user_id);

        $this->CI->load->library('plans_lib');
        $this->CI->load->library('orders_lib');

        $this->CI->load->library('warehouse_lib');
        $warehouse = $this->CI->warehouse_lib->getByID($record->warehouse_id);

        $order = $this->CI->orders_lib->getByID($record->id);

        $skip_couriers = array();

        if (!empty($record->allocation_skip_courier)) {
            $s_cs = explode(',', $record->allocation_skip_courier);
            $skip_couriers = array_merge($skip_couriers, $s_cs);
        }
        $skip_couriers[] = $record->courier_id;
        $skip_couriers = array_unique($skip_couriers);

        $courier_id = $this->CI->orders_lib->getCourierIdForCustomPlan($record->user_id, $record->courier_slab, $order, $warehouse, $skip_couriers);
        if (empty($courier_id))
            return false;

        $update = array(
            'courier_id' => $courier_id,
            'allocation_skip_courier' => implode(',', $skip_couriers),
        );

        $this->update($record->shipment_id, $update);

        if ($is_api_shipment) {
            return $courier_id;
        }

        do_action('shipping.new', $record->shipment_id, $courier_id);

        return true;
    }

    function autoInvoice($shipment_ids = false, $format = 'thermal', $user_id = false)
    {
        ini_set("pcre.backtrack_limit", "50000000");
        if (empty($shipment_ids))
            return false;

        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => './temp',
            'mode' => 'utf-8',
            'margin_left' => 5,
            'margin_top' => 5,
            'margin_right' => 5,
            'margin_bottom' => 5,
        ]);

        $shipment_data = array();
        foreach ($shipment_ids as $shipment_id) {
            $order = $this->getInvoiceData($shipment_id);

            if (!in_array($order->shipment_details->ship_status, array('cancelled', 'new')))
                $shipment_data[] = $order;
        }

        if (empty($shipment_data))
            return false;

        $pdf_content = $this->CI->load->view('orders/generate_invoice', array('shipments' => $shipment_data, 'format' => $format), true);
        //pr($pdf_content,1);
        $mpdf->WriteHTML($pdf_content);

        $this->CI->load->library('s3');

        $directory = 'assets/order_invoice/';
        $file_name = date('YmdHis') . '-' . rand(10, 99) . '.pdf';
        $mpdf->Output($directory . $file_name, 'F');
        $aws_file_name = $this->CI->s3->amazonS3Upload($file_name, $directory . $file_name, 'order_invoice');
        //unlink($directory . $file_name);
        return $aws_file_name;
    }

    function getInvoiceData($shipment_id = false)
    {
        if (!$shipment = $this->getByID($shipment_id))
            return false;

        $shimmentDetails = array();

        $this->CI->load->library('orders_lib');
        $invoiceDate = $this->CI->orders_lib->createOrderInvoiceDate($shipment->user_id, $shipment_id);

        $order = $this->CI->orders_lib->getByID($shipment->order_id);

        $products = $this->CI->orders_lib->getOrderProducts($order->id);

        $this->CI->load->config('pincodes');

        $this->origin_state_code = '';
        $this->origin_state = '';

        $this->gst_state_codes = $this->CI->config->item('gst_state_codes');
        $pincode_states = $this->CI->config->item('pincode_states');

        if (!empty($order->billing_fname) && (!empty($order->billing_zip) || !empty($order->billing_gst_number))) {
            $destination_state_code = '';
            if (!empty($order->billing_gst_number)) {
                $gst_state = substr($order->billing_gst_number, 0, 2);
                $destination_state_code = $gst_state;
                $destination_state = array_key_exists($destination_state_code, $this->gst_state_codes) ? $this->gst_state_codes[$destination_state_code] : '';
            }
            if (empty($destination_state_code)) {
                $destination_two_digit = substr($order->billing_zip, 0, 2);
                $destination_state = isset($pincode_states[$destination_two_digit]) ? $pincode_states[$destination_two_digit] : false;
                $destination_state_code = array_search(strtolower($destination_state), $this->gst_state_codes);
            }
        } else {
            $destination_two_digit = substr($order->shipping_zip, 0, 2);
            $destination_state = isset($pincode_states[$destination_two_digit]) ? $pincode_states[$destination_two_digit] : false;
            $destination_state_code = array_search(strtolower($destination_state), $this->gst_state_codes);
        }

        $warehouse = false;
        if (!empty($shipment)) {
            $this->CI->load->library('profile_lib');
            $companyDetails = $this->CI->profile_lib->getprofileByUserID($order->user_id);
            $this->CI->load->library('courier_lib');
            $courier = $this->CI->courier_lib->getByID($shipment->courier_id);

            $this->CI->load->library('warehouse_lib');
            if ($shipment->warehouse_id == '0') {
                $warehouse = $this->CI->warehouse_lib->getUserWarehouse($order->user_id);
            } else {
                $warehouse = $this->CI->warehouse_lib->getByID($shipment->warehouse_id);
            }

            if (!empty($warehouse->gst_number)) {
                $gst_state = substr($warehouse->gst_number, 0, 2);
                $this->origin_state_code = $gst_state;
                $this->origin_state = array_key_exists($this->origin_state_code, $this->gst_state_codes) ? $this->gst_state_codes[$this->origin_state_code] : '';
            } else {
                $gst_state = array_search(strtolower($warehouse->state), $this->gst_state_codes);
                if ($gst_state) {
                    $this->origin_state_code = $gst_state;
                    $this->origin_state = $warehouse->state;
                }
            }
        }

        $product_order = array();
        $prod = array();
        if (!empty($products)) {
            foreach ($products as $key => $value) {
                $prod['id'] = $value->id;
                $prod['order_id'] = $value->order_id;
                $prod['product_id'] = $value->product_id;
                $prod['product_name'] = $value->product_name;
                $prod['product_qty'] = $value->product_qty;
                $prod['product_sku'] = $value->product_sku;
                $prod['product_weight'] = $value->product_weight;
                $prod['product_price'] = $value->product_price;
                $prod['length'] = '';
                $prod['breadth'] = '';
                $prod['height'] = '';
                $prod['weight'] = '';
                $prod['igst'] = 0;
                $prod['hsn_code'] = '';

                // if (!empty($value->product_sku)) {
                //     $product = $this->CI->orders_lib->getOrderProductsWith($value->product_sku, $order->user_id, "product_sku");
                //     if (!empty($product)) {
                //         $prod['length'] = $product->length;
                //         $prod['breadth'] = $product->breadth;
                //         $prod['height'] = $product->height;
                //         $prod['weight'] = $product->weight;
                //         $prod['igst'] = $product->igst;
                //         $prod['hsn_code'] = $product->hsn_code;
                //         $product_order[$key] = (object)$prod;
                //         continue;
                //     }
                // }
                // if (!empty($value->product_name)) {
                //     $product = $this->CI->orders_lib->getOrderProductsWith($value->product_name, $order->user_id, "product_name");
                //     if (!empty($product)) {
                //         $prod['length'] = $product->length;
                //         $prod['breadth'] = $product->breadth;
                //         $prod['height'] = $product->height;
                //         $prod['weight'] = $product->weight;
                //         $prod['igst'] = $product->igst;
                //         $prod['hsn_code'] = $product->hsn_code;
                //         $product_order[$key] = (object)$prod;
                //         continue;
                //     }
                // }

                $this->CI->load->library('products_lib');
                $code = $this->CI->products_lib->get_product_details_billing_code($order->user_id, $prod);

                if (!empty($code)) {
                    $product = $this->CI->products_lib->getProductBillingDetailsByCode($order->user_id, $code);

                    if (!empty($product)) {
                        if (isset($product->igst) && isset($product->hsn_code)) {
                            $prod['igst'] = $product->igst;
                            $prod['hsn_code'] = $product->hsn_code;
                            $product_order[$key] = (object)$prod;
                            continue;
                        }
                    }
                }
                $product_order[$key] = (object)$prod;
            }
        }
        $shimmentDetails['awb_number'] = $shipment->awb_number;
        $shimmentDetails['ship_status'] = $shipment->ship_status;
        $shimmentDetails['origin_state_code'] = $this->origin_state_code;
        $shimmentDetails['origin_state'] = $this->origin_state;
        $shimmentDetails['destination_state_code'] = $destination_state_code;
        $shimmentDetails['destination_state'] = $destination_state;
        $shimmentDetails['invoice_date'] = $invoiceDate->created;
        $shimmentDetails['created'] = $shipment->created;


        $this->CI->load->library('Profile_lib');
        $invoice_settings_data = array();
        $invoice_setting_data = $this->CI->profile_lib->get_invoice_setting($order->user_id);
        if (!empty($invoice_setting_data)) {
            $invoice_settings_data['hide_compony'] = !empty($invoice_setting_data->hide_compony) ? $invoice_setting_data->hide_compony : '';
            $invoice_settings_data['invoice_prefix'] = !empty($invoice_setting_data->invoice_prefix) ? $invoice_setting_data->invoice_prefix : '';
            $invoice_settings_data['invoice_banner'] = !empty($invoice_setting_data->invoice_banner) ? $invoice_setting_data->invoice_banner : '';
            $invoice_settings_data['invoice_signature'] = !empty($invoice_setting_data->invoice_signature) ? $invoice_setting_data->invoice_signature : '';
            $invoice_settings_data['custom_name'] = !empty($invoice_setting_data->custom_name) ? $invoice_setting_data->custom_name : '';
            $invoice_settings_data['custom_value'] = !empty($invoice_setting_data->custom_value) ? $invoice_setting_data->custom_value : '';
        }

        $channel_brand_logo = '';
        if (!empty($order->channel_id)) {
            $channel_brand_logo = array();
            $channel_brands_logo = $this->CI->profile_lib->get_channel_data($order->channel_id);
            if (!empty($channel_brands_logo)) {
                $channel_brand_logo['channel_brand_logo'] = !empty($channel_brands_logo->brand_logo) ? $channel_brands_logo->brand_logo : '';
            }
        }


        $return = array(
            'order' => $order,
            'products' => $product_order,
            'warehouse' => $warehouse,
            'courier' => $courier,
            'company_details' => $companyDetails,
            'shipment_details' => (object)$shimmentDetails,
            'invoice_settings' => (object)$invoice_settings_data,
            'channels_brand_logo' => (object)$channel_brand_logo
        );

        return (object) $return;
    }

    function pushShopifyStatus($shipment_id = false, $channel = false)
    {
        if (!$shipment_id)
            return false;

        $shipment = $this->getByID($shipment_id);

        if (empty($shipment))
            return false;

        $this->CI->load->library('orders_lib');
        $order = $this->CI->orders_lib->getByID($shipment->order_id);

        if (empty($order) || empty($order->api_order_id))
            return false;

        $this->CI->load->library('channels_lib');
        $channel = $this->CI->channels_lib->getByID($order->channel_id);
        if (empty($channel))
            return false;
        
        switch ($channel->channel) {
            case 'shopify':
            case 'shopify_oneclick':
                if (empty($channel) || $channel->auto_update_status != '1') {
                    return false;
                }

                $ship_status = strtolower(str_replace(' ', '_', $shipment->ship_status));
                $config = array(
                    'channel_id' => $channel->id
                );

                $this->CI->load->library('channels/shopify');
                $shopify = new Shopify($config);

                $shopify->shopify_status_update($order->api_order_id, $ship_status);
                break;

            default:
                return false;
        }
    }

public function club_wallet_history($date = null)
{
    
    if (!$date) {
        $date = date('Y-m-d', strtotime('yesterday'));
    }

    $startOfDay = strtotime($date . " 00:00:00");
    $endOfDay   = strtotime($date . " 23:59:59");

   
    $query = $this->CI->db
        ->select("
            user_id, 
            txn_for, 
            SUM(CAST(amount AS DECIMAL(10,2))) as total_amount,
            MIN(balance_before) as balance_before,
            MAX(balance_after) as balance_after,
            type
        ", FALSE) 
        ->from('tbl_communication_wallet_history')
        ->where("created >=", $startOfDay)
        ->where("created <=", $endOfDay)
        ->group_by('user_id, txn_for')
        ->get();

    $results = $query->result_array();

    if (empty($results)) {
        return [
            'status'  => 'success',
            'date'    => $date,
            'message' => 'No wallet history found for this date.',
            'count'   => 0
        ];
    }

   
    $this->CI->db->trans_start();

    foreach ($results as $row) {
       
        $exists = $this->CI->db->where('user_id', $row['user_id'])
            ->where('txn_for', $row['txn_for'])
            ->where('created', $startOfDay)
            ->count_all_results('tbl_wallet_history');

        if ($exists > 0) {
            continue; 
        }

        
        $noteMessage = "Communication charge for {$row['txn_for']} dated {$date}";

        $insertData = [
            'user_id'        => $row['user_id'],
            'txn_for'        => $row['txn_for'],
            'txn_ref'        => '',
            'ref_id'         => '0',
            'balance_before' => $row['balance_before'],
            'amount'         => $row['total_amount'],
            'balance_after'  => $row['balance_after'],
            'type'           => $row['type'],
            'notes'          => $noteMessage,
            'created'        => $startOfDay
        ];

        $this->CI->db->insert('tbl_wallet_history', $insertData);
    }

    
    $this->CI->db->trans_complete();

    if ($this->CI->db->trans_status() === FALSE) {
        return [
            'status'  => 'error',
            'date'    => $date,
            'message' => 'Database error while inserting wallet history.'
        ];
    }

    return [
        'status' => 'success',
        'date'   => $date,
        'count'  => count($results),
        'data'   => $results
    ];
}

}
