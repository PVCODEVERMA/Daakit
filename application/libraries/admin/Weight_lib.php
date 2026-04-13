<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Weight_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('admin/weight_model');
        $this->CI->load->library('admin/shipping_lib');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->weight_model, $method)) {
            throw new Exception('Undefined method weight_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->weight_model, $method], $arguments);
    }

    function weightFileUpload()
    {
        $this->CI->load->library('form_validation');
        $config = array(
            array(
                'field' => 'importFile',
                'label' => 'Import File',
                'rules' => 'callback_file_check'
            ),
        );

        $this->CI->form_validation->set_rules($config);

        if (!$this->CI->form_validation->run()) {
            $this->error = validation_errors();
            return false;
        }


        if (is_uploaded_file($_FILES['importFile']['tmp_name'])) {
            // Load CSV reader library
            $this->CI->load->library('csvreader');
            // Parse data from CSV file
            $csvData = $this->CI->csvreader->parse_csv($_FILES['importFile']['tmp_name']);
            if (empty($csvData)) {
                $this->error = 'Blank CSV File';
                return false;
            }

            foreach ($csvData as $row_key => $row) {
                if (!$this->validate_weight_file_data($row)) {
                    $this->error = 'Row no. ' . ($row_key + 1) . $this->error;
                    return false;
                }
            }


            $update = array();
            foreach ($csvData as $row2) {

                $update[] = array(
                    'awb_number' => $row2['AWB Number'],
                    'courier_billed_weight' => !empty($row2['Billed Weight']) ? $row2['Billed Weight'] : '0',
                    'courier_actual_weight' => !empty($row2['Actual Weight']) ? $row2['Actual Weight'] : '0',
                    'courier_volumetric_weight' => !empty($row2['Volumetric Weight']) ? $row2['Volumetric Weight'] : '0',
                    'courier_length' => !empty($row2['Length']) ? $row2['Length'] : '0',
                    'courier_breadth' => !empty($row2['Breadth']) ? $row2['Breadth'] : '0',
                    'courier_height' => !empty($row2['Height']) ? $row2['Height'] : '0',
                    'weight_upload_date' => time(),
                );
            }

            $this->updateBulkWeightByAWB($update);

            return true;
        }
    }

    function creditFileUpload()
    {
        $this->CI->load->library('form_validation');
        $config = array(
            array(
                'field' => 'importFile',
                'label' => 'Import File',
                'rules' => 'callback_file_check'
            ),
        );

        $this->CI->form_validation->set_rules($config);

        if (!$this->CI->form_validation->run()) {
            $this->error = validation_errors();
            return false;
        }



        if (is_uploaded_file($_FILES['importFile']['tmp_name'])) {
            // Load CSV reader library
            $this->CI->load->library('csvreader');
            // Parse data from CSV file
            $csvData = $this->CI->csvreader->parse_csv($_FILES['importFile']['tmp_name']);
            if (empty($csvData)) {
                $this->error = 'Blank CSV File';
                return false;
            }

            foreach ($csvData as $row_key => $row) {
                if (!$this->validate_credit_file_data($row)) {
                    $this->error = 'Row no. ' . ($row_key + 1) . $this->error;
                    return false;
                }
            }

            $this->CI->load->library('admin/apply_weight');

            foreach ($csvData as $row2) {
                $awb_number = $row2['AWB Number'];
                $weight = $row2['Billed Weight'];

                $shipment = $this->CI->shipping_lib->getByAWB($awb_number);

                if (empty($shipment) || $shipment->extra_weight_charges <= 0 || $shipment->charged_weight <= $weight)
                    continue;

                $apply_weight = new Apply_weight();
                $apply_weight->setShipmentID($shipment->id);
                $apply_weight->setWonBy('Courier');
                $apply_weight->setFinalWeight($weight);

                $apply_weight->applyCNForWeight();
            }

            return true;
        }
    }

    private function validate_weight_file_data($data)
    {
        $this->CI->form_validation->set_data($data);

        $this->CI->form_validation->set_message('required', '%s is required');
        $this->CI->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');

        $config = array(
            array(
                'field' => 'AWB Number',
                'label' => 'AWB Number',
                'rules' => 'trim|required|alpha_numeric|min_length[4]|max_length[20]',
            ),
            array(
                'field' => 'Billed Weight',
                'label' => 'Billed Weight',
                'rules' => 'trim|required|numeric|greater_than_equal_to[500]',
            ),
            array(
                'field' => 'Actual Weight',
                'label' => 'Actual Weight',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'Volumetric Weight',
                'label' => 'Volumetric Weight',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'Length',
                'label' => 'Length',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'Height',
                'label' => 'Height',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'Breadth',
                'label' => 'Breadth',
                'rules' => 'trim|numeric',
            ),
        );

        $this->CI->form_validation->set_rules($config);

        if ($this->CI->form_validation->run()) {
            return true;
        } else {
            $this->error = validation_errors();
            return false;
        }
    }

    private function validate_credit_file_data($data)
    {
        $this->CI->form_validation->set_data($data);

        $this->CI->form_validation->set_message('required', '%s is required');
        $this->CI->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');

        $config = array(
            array(
                'field' => 'AWB Number',
                'label' => 'AWB Number',
                'rules' => 'trim|required|alpha_numeric|min_length[4]|max_length[20]',
            ),
            array(
                'field' => 'Billed Weight',
                'label' => 'Billed Weight',
                'rules' => 'trim|required|numeric|greater_than_equal_to[500]',
            ),
        );

        $this->CI->form_validation->set_rules($config);

        if ($this->CI->form_validation->run()) {
            return true;
        } else {
            $this->error = validation_errors();
            return false;
        }
    }

    function applyWeight($id = false)
    {
        if (!$id)
            return false;



        $shipment = $this->CI->shipping_lib->getByID($id);

        if (!$shipment)
            return false;

        if ($shipment->weight_applied_date > '0' || $shipment->courier_billed_weight <= 0)
            return false;

        $weight = $shipment->courier_billed_weight;

        $this->CI->load->library('admin/orders_lib');
        $order = $this->CI->orders_lib->getByID($shipment->order_id);

        if (!$order)
            return false;

        $this->CI->load->library('courier_lib');
        $courier = $this->CI->courier_lib->getByID($shipment->courier_id);

        if (!$courier)
            return false;

        $this->CI->load->library('warehouse_lib');
        $warehouse = $this->CI->warehouse_lib->getByID($shipment->warehouse_id);

        if (!$warehouse)
            return false;

        $this->CI->load->library('admin/user_lib');
        $user = $this->CI->user_lib->getByID($order->user_id);

        $this->CI->load->library('pricing_lib');
        $pricing = new Pricing_lib();

        $pricing->setPlan($user->pricing_plan);
        $pricing->setCourier($shipment->courier_id);
        if (strtolower($order->order_payment_type) == 'reverse') {
            $pricing->setOrigin($order->shipping_zip);
            $pricing->setDestination($warehouse->zip);
            $pricing->setType('reverse');
        } else {
            $pricing->setOrigin($warehouse->zip);
            $pricing->setDestination($order->shipping_zip);
        }
        $pricing->setWeight($weight);
        // $pricing->setLength($order->package_length);
        // $pricing->setHeight($order->package_height);
        // $pricing->setBreadth($order->package_breadth);
        $shipping_cost = $pricing->calculateCost();

        if (empty($shipping_cost))
            return false;

        $chargeable_amount = $shipping_cost['courier_charges'];
        $charged_amount = $shipment->courier_fees;

        $this->CI->load->library('wallet_lib');

        $save = array(
            'charged_weight' => $weight,
            'weight_applied_date' => time(),
        );

        if ($chargeable_amount > $charged_amount) {
            $extra_charge = round($chargeable_amount - $charged_amount, 2);
            $save['pending_weight_charges'] = $extra_charge;
            $remittance_hold_amount = $extra_charge;
            if ($shipment->ship_status == 'rto') {
                $remittance_hold_amount += $remittance_hold_amount;
            }

            $this->CI->user_lib->hold_release_remittance($shipment->user_id, $remittance_hold_amount, 'hold');
        }

        $this->CI->shipping_lib->update($shipment->id, $save);

        return true;
    }

    function closeWeightDispute($shipment_id = false, $won_by = false)
    {
        if (!$shipment_id || !$won_by)
            return false;


        $shipment = $this->CI->shipping_lib->getByID($shipment_id);

        if (empty($shipment))
            return false;

        //apply shipping charges

        if ($shipment->extra_weight_charges > 0) {
            return $this->applyProcessedExtraWeightCharges($shipment->id, $won_by);
        } elseif ($shipment->pending_weight_charges <= 0) {
            return $this->applyPendingWeightCharges($shipment->id, $won_by);
        } else {
            return false;
        }
    }

    function applyProcessedExtraWeightCharges($shipment_id = false, $won_by = false)
    {
        if (!$shipment_id)
            return false;

        if (!in_array($won_by, array('courier', 'seller')))
            return false;

        $shipment = $this->CI->shipping_lib->getByID($shipment_id);

        if (!$shipment || $shipment->extra_weight_charges <= 0 || $shipment->weight_credit_applied == '1' || $shipment->weight_dispute_closed == '1')
            return false;


        switch ($won_by) {
            case 'courier':
                break;
            case 'seller':

                break;
            default:
                return false;
        }
    }


    function applyPendingWeightCharges($shipment_id = false, $won_by = false)
    {
        if (!$shipment_id)
            return false;

        if (!in_array($won_by, array('courier', 'seller')))
            return false;

        $shipment = $this->CI->shipping_lib->getByID($shipment_id);
        if (!$shipment || $shipment->extra_weight_charges > 0 || $shipment->pending_weight_charges <= 0 || $shipment->weight_dispute_closed == '1')
            return false;

        $update = array(
            'pending_weight_charges' => '0',
        );

        if ($shipment->weight_dispute_raised == '1') {
            $update['weight_dispute_closed'] = '1';
        }

        //get user details
        $this->CI->load->library('user_lib');
        $user = $this->CI->user_lib->getByID($shipment->user_id);

        if (empty($user))
            return false;

        $this->CI->load->library('plans_lib');
        $plan = $this->CI->plans_lib->getPlanByName($user->pricing_plan);
        
        if (empty($plan))
            return false;

        $plan_type = $plan->plan_type;

        switch ($won_by) {
            case 'courier':
                //charge balance from customer wallet
                $this->CI->load->library('wallet_lib');

                $wallet = new Wallet_lib();
                $wallet->setUserID($shipment->user_id);
                $wallet->setAmount($shipment->pending_weight_charges);
                $wallet->setTransactionType('debit');
                $wallet->setNotes('Extra Weight Charges Applied');
                $wallet->setTxnFor('shipment');
                $wallet->setRefID($shipment->id);
                $wallet->setTxnRef('extra_weight');
                $wallet->creditDebitWallet();

                $update['extra_weight_charges'] = $shipment->pending_weight_charges;

                if (($plan_type != 'per_dispatch') && ($shipment->ship_status == 'rto')) {
                    $wallet = new Wallet_lib();
                    $wallet->setUserID($shipment->user_id);
                    $wallet->setAmount($shipment->pending_weight_charges);
                    $wallet->setTransactionType('debit');
                    $wallet->setNotes('RTO Extra Weight Charges Applied');
                    $wallet->setTxnFor('shipment');
                    $wallet->setRefID($shipment->id);
                    $wallet->setTxnRef('rto_extra_weight');
                    $wallet->creditDebitWallet();

                    $update['rto_extra_weight_charges'] = $shipment->pending_weight_charges;
                }

                break;
            case 'seller':
                break;
            default:
                return false;
        }

        $this->CI->shipping_lib->update($shipment->id, $update);

        //release hold remittance
        $this->CI->load->library('admin/user_lib');

        $this->CI->user_lib->hold_release_remittance($shipment->user_id, $shipment->pending_weight_charges, 'release');

        return true;
    }
}
