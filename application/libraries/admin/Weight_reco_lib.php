<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Weight_reco_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('admin/weight_reco_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->weight_reco_model, $method)) {
            throw new Exception('Undefined method weight_reco_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->weight_reco_model, $method], $arguments);
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

            if (count($csvData) > 10000) {
                $this->error = 'Maximum 10000 rows are allowed';
                return false;
            }

            foreach ($csvData as $row_key => $row) {
                if (!$this->validate_weight_file_data($row)) {
                    $this->error = 'Row no. ' . ($row_key + 1) . $this->error;
                    return false;
                }
            }


            $this->CI->load->library('admin/shipping_lib');
            $this->CI->load->library('admin/orders_lib');
            $this->CI->load->library('admin/user_lib');
            $this->CI->load->library('courier_lib');
            $this->CI->load->library('warehouse_lib');
            $this->CI->load->library('pricing_lib');

            $csv = new Csv_lib();
            $csv->add_row(array('AWB Number', 'Billed Weight',  'Volumetric Weight', 'Length', 'Breadth', 'Height', 'Remarks', 'Message'));


            $update = array();
            foreach ($csvData as $row2) {

                $awb = $row2['AWB Number'];
                $courier_billed_weight = !empty($row2['Billed Weight']) ? $row2['Billed Weight'] : '0';
                $courier_vol_weight = !empty($row2['Volumetric Weight']) ? $row2['Volumetric Weight'] : '0';
                $courier_length = !empty($row2['Length']) ? $row2['Length'] : '0';
                $courier_breadth = !empty($row2['Breadth']) ? $row2['Breadth'] : '0';
                $courier_height = !empty($row2['Height']) ? $row2['Height'] : '0';
                $courier_remarks = !empty($row2['Remarks']) ? $row2['Remarks'] : '';

                $csv_row = array(
                    $awb,
                    $courier_billed_weight,
                    $courier_vol_weight,
                    $courier_length,
                    $courier_breadth,
                    $courier_height,
                    $courier_remarks
                );
                $shipment = $this->CI->shipping_lib->getByAWB($row2['AWB Number']);
                 if (empty($shipment)) {
                     $csv_row[] = 'AWB not found';
                     $csv->add_row($csv_row);
                     continue;
                 }
                 
  
                 $products = $this->CI->user_lib->getOrderProducts($shipment->order_id);
 
                 
                 if (!empty($products))
                 {
                    foreach ($products as $prod)
                     {
                         $pr=array(
                             "product_sku"=>!empty($prod->product_sku) ? $prod->product_sku : '',
                             "product_name"=>!empty($prod->product_name) ? $prod->product_name : '',
                             "product_qty"=>!empty($prod->product_qty) ? $prod->product_qty : '',
                          );
                    
                         $product_details = $this->CI->user_lib->get_data_code($this->CI->user_lib->get_product_details_code( $shipment->user_id,$pr));
                         if(!empty($product_details) && $product_details->weight_locked=='2'  ) 
                         {
                            $courier_billed_weight= !empty($shipment->calculated_weight) ? $shipment->calculated_weight : '0' ;   
                             $csv_row = array(
                                 $awb,
                                 $courier_billed_weight,
                                 $courier_vol_weight,
                                 $courier_length,
                                 $courier_breadth,
                                 $courier_height,
                                 $courier_remarks
                             );
                         }
                     }
                 }

                if((isset($shipment->base_freight)) && (empty($shipment->base_freight))){
                    $csv_row[] = 'Base freight is empty';
                    $csv->add_row($csv_row);
                    continue;  
                }

                $order = $this->CI->orders_lib->getByID($shipment->order_id);

                if (empty($order)) {
                    $csv_row[] = 'Order details not found';
                    $csv->add_row($csv_row);
                    continue;
                }
                $user = $this->CI->user_lib->getByID($shipment->user_id);
                if (empty($user)) {
                    $csv_row[] = 'User details not found';
                    $csv->add_row($csv_row);
                    continue;
                }
                $warehouse = $this->CI->warehouse_lib->getByID($shipment->warehouse_id);
                if (!$warehouse) {
                    $csv_row[] = 'Pickup details not found';
                    $csv->add_row($csv_row);
                    continue;
                }
                $courier = $this->CI->courier_lib->getByID($shipment->courier_id);
                $weight_record = $this->getByShipmentID($shipment->id);

                if (!empty($weight_record) && $weight_record->weight_applied == '1') {
                    $csv_row[] = 'Weight already applied';
                    $csv->add_row($csv_row);
                    continue;
                }

                $save = array(
                    'shipment_id' => $shipment->id,
                    'user_id' => $shipment->user_id,
                    'courier_billed_weight' => $courier_billed_weight,
                    'courier_vol_weight' => $courier_vol_weight,
                    'courier_length' => $courier_length,
                    'courier_breadth' => $courier_breadth,
                    'courier_height' => $courier_height,
                    'upload_remarks' => $courier_remarks,
                    'seller_dead_weight' => !empty($order->package_weight) ? $order->package_weight : '0',
                    'seller_booking_weight' => !empty($shipment->calculated_weight) ? $shipment->calculated_weight : '',
                    'seller_package_length' => (!empty($order->package_length)) ? $order->package_length : '0',
                    'seller_package_breadth' => (!empty($order->package_breadth)) ? $order->package_breadth : '0',
                    'seller_package_height' => (!empty($order->package_height)) ? $order->package_height : '0',
                    'upload_weight_difference' => round($courier_billed_weight - $shipment->calculated_weight),
                    'upload_date' => time(),
                    'seller_action_status' => 'no dispute',
                );
                if ($shipment->calculated_weight >= $courier_billed_weight) {
                    $save['weight_difference_charges'] = '0';
                } else {
                    $pricing = $this->getPricing($shipment, $warehouse, $user, $order, $courier_billed_weight, $courier_length, $courier_breadth, $courier_height);

                    $shipping_cost = $pricing->calculateCost();
                    
                    if (empty($shipping_cost)) {
                        $csv_row[] = 'Unable to calculate freight';
                        $csv->add_row($csv_row); 
                        continue;
                    }

                    $chargeable_amount = $shipping_cost['courier_charges'];

                    $old_shipping_charges = $shipment->courier_fees;

                    $new_extra_charges = max(round($chargeable_amount - $old_shipping_charges, 2), 0);

                    $additional_weight = round($shipping_cost['calculated_weight'] - $shipment->calculated_weight);

                    $save['upload_weight_difference'] = $additional_weight;
                    $save['weight_difference_charges'] = $new_extra_charges;
                    $save['weight_new_slab'] = $shipping_cost['calculated_weight'];
                    if(($new_extra_charges) > 0){
                    $save['seller_action_status'] = 'open';
                    }
                }
                //pr($save,1);
                if (!empty($weight_record))
                    $this->update($weight_record->id, $save);
                else
                    $this->create($save);

                $csv_row[] = 'Success';
                $csv->add_row($csv_row);
            }
            $csv->export_csv();
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
                'rules' => 'trim|required|alpha_dash|min_length[3]|max_length[20]',
            ),
            array(
                'field' => 'Billed Weight',
                'label' => 'Billed Weight',
                'rules' => 'trim|required|numeric|greater_than[0]',
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

    function bulkActionimport($user_id = false)
    {
        $this->CI->load->library('form_validation');
        $config = array(
            array(
                'field' => 'importFile',
                'label' => 'Import File',
                'rules' => 'callback_file_check'
            ),
            array(
                'field' => 'action',
                'label' => 'Action',
                'rules' => 'trim|required'
            ),
        );

        $this->CI->form_validation->set_rules($config);

        if (!$this->CI->form_validation->run()) {
            $this->error = validation_errors();
            return false;
        }

        if (!is_uploaded_file($_FILES['importFile']['tmp_name'])) {
            $this->error = 'Invalid file';
            return false;
        }

        $this->CI->load->library('csvreader');
        // Parse data from CSV file
        $csvData = $this->CI->csvreader->parse_csv($_FILES['importFile']['tmp_name']);
        if (empty($csvData)) {
            $this->error = 'Blank CSV File';
            return false;
        }

        if (count($csvData) > 10000) {
            $this->error = 'Maximum 10000 rows are allowed';
            return false;
        }

        switch (strtolower($this->CI->input->post('action'))) {
            case 'revert_extra_charges':
                return $this->revert_extra_charges($csvData);
                break;
            case 'apply_weight':
                return $this->apply_weight($csvData);
                break;
            case 'charge_to_wallet':
                return $this->charge_to_wallet($csvData);
                break;
            case 'close_dispute_courier_favour':
                return $this->close_dispute_courier_favour($csvData, $user_id);
                break;
            case 'close_dispute_seller_favour':
                return $this->close_dispute_seller_favour($csvData, $user_id);
                break;
            case 'close_dispute_new_weight':
                return $this->close_dispute_new_weight($csvData, $user_id);
                break;
            case 'issue_credit_note':
                return $this->issue_credit_note($csvData, $user_id);
                break;
            default:
                $this->error = 'Action not supported';
                return false;
        }
    }

    private function issue_credit_note($csvData = array(), $user_id = false)
    {
        foreach ($csvData as $row_key => $row) {
            if (!$this->validate_new_weight_file_data($row)) {
                $this->error = 'Row no. ' . ($row_key + 1) . $this->error;
                return false;
            }
        }

        $this->CI->load->library('admin/shipping_lib');
        $this->CI->load->library('admin/orders_lib');
        $this->CI->load->library('admin/user_lib');
        $this->CI->load->library('courier_lib');
        $this->CI->load->library('warehouse_lib');
        $this->CI->load->library('pricing_lib');

        $csv = new Csv_lib();
        $csv->add_row(array('AWB Number', 'Weight', 'Message'));

        $update = array();
        foreach ($csvData as $row2) {

            $awb = $row2['AWB Number'];
            $weight = $row2['Weight'];

            $csv_row = array(
                $awb,
                $weight
            );

            //get shipment data
            $shipment = $this->CI->shipping_lib->getByAWB($awb);
            if (empty($shipment)) {
                $csv_row[] = 'AWB not found';
                $csv->add_row($csv_row);
                continue;
            }

            $order = $this->CI->orders_lib->getByID($shipment->order_id);

            if (empty($order)) {
                $csv_row[] = 'Order details not found';
                $csv->add_row($csv_row);
                continue;
            }

            $user = $this->CI->user_lib->getByID($shipment->user_id);

            if (empty($user)) {
                $csv_row[] = 'User details not found';
                $csv->add_row($csv_row);
                continue;
            }

            $warehouse = $this->CI->warehouse_lib->getByID($shipment->warehouse_id);

            if (!$warehouse) {
                $csv_row[] = 'Pickup details not found';
                $csv->add_row($csv_row);
                continue;
            }

            $courier = $this->CI->courier_lib->getByID($shipment->courier_id);

            $weight_record = $this->getByShipmentID($shipment->id);

            if (empty($weight_record)) {
                $csv_row[] = 'Weight not uploaded';
                $csv->add_row($csv_row);
                continue;
            }

            if ($weight_record->is_cn_issued == '1') {
                $csv_row[] = 'CN already issued';
                $csv->add_row($csv_row);
                continue;
            }

            if (!in_array($weight_record->seller_action_status, array('dispute closed', 'accepted', 'auto accepted'))) {
                $csv_row[] = 'Weight reco. status not in dispute closed, accepeted, auto accepted';
                $csv->add_row($csv_row);
                continue;
            }

            if ($shipment->extra_weight_charges <= '0') {
                $csv_row[] = 'No extra weight charges exists';
                $csv->add_row($csv_row);
                continue;
            }

            $save = array(
                'dispute_closure_favour' => $weight
            );

            $pricing = $this->getPricing($shipment, $warehouse, $user, $order, @$weight, @$courier_length, @$courier_breadth, @$courier_height);
            $shipping_cost = $pricing->calculateCost();

            if (empty($shipping_cost)) {
                $csv_row[] = 'Unable to calculate freight';
                $csv->add_row($csv_row);
                continue;
            }

            if ($shipment->charged_weight <= $weight) {
                $csv_row[] = 'Last applied weight is ' . $shipment->charged_weight;
                $csv->add_row($csv_row);
                continue;
            }

            $chargeable_amount = $shipping_cost['courier_charges'];

            $old_shipping_charges = $shipment->courier_fees;

            $new_extra_charges = max(round($chargeable_amount - $old_shipping_charges, 2), 0);


            $additional_weight = round($shipping_cost['calculated_weight'] - $shipment->calculated_weight);

            $save['upload_weight_difference'] = round($additional_weight);
            $save['weight_difference_charges'] = $new_extra_charges;
            $save['weight_new_slab'] = $shipping_cost['calculated_weight'];
            $save['is_cn_issued'] = '1';

            $save_shipment = array(
                'charged_weight' => $shipping_cost['calculated_weight'],
                'extra_weight_charges' => $new_extra_charges,
                'rto_extra_weight_charges' => '0',
            );

            if (strtolower($shipment->ship_status) == 'rto') {
                $save_shipment['rto_extra_weight_charges'] = $new_extra_charges;
            }

            if ($shipment->extra_weight_charges <= $new_extra_charges) {
                $csv_row[] = 'Previous exra weight charges are less than current charges';
                $csv->add_row($csv_row);
                continue;
            }

            //now calculae the accurate charges to the shipment

            //revert the amount to seller

            $amount_to_revert = round($shipment->extra_weight_charges - $new_extra_charges, 2);

            if (!$this->revert_weight_charges($shipment, $amount_to_revert)) {
                $csv_row[] = $this->error;
                $csv->add_row($csv_row);
                continue;
            }

            $this->update($weight_record->id, $save);

            $this->CI->shipping_lib->update($shipment->id, $save_shipment);
            $csv_row[] = 'Success';
            $csv->add_row($csv_row);
        }

        $csv->export_csv();

        return true;
    }

    private function close_dispute_new_weight($csvData = array(), $user_id = false)
    {
        foreach ($csvData as $row_key => $row) {
            if (!$this->validate_new_weight_file_data($row)) {
                $this->error = 'Row no. ' . ($row_key + 1) . $this->error;
                return false;
            }
        }

        $this->CI->load->library('admin/shipping_lib');
        $this->CI->load->library('admin/orders_lib');
        $this->CI->load->library('admin/user_lib');
        $this->CI->load->library('courier_lib');
        $this->CI->load->library('warehouse_lib');
        $this->CI->load->library('pricing_lib');

        $csv = new Csv_lib();
        $csv->add_row(array('AWB Number', 'Weight', 'Remarks', 'Image Urls', 'Message'));

        $update = array();
        foreach ($csvData as $row2) {
            $remarks = $image_urls = '';
            $awb = $row2['AWB Number'];
            $weight = $row2['Weight'];
            if (isset($row2['Remarks'])) {
                $remarks = $row2['Remarks'];
            }
            if (isset($row2['Image Urls'])) {
                $image_urls = $row2['Image Urls'];
            }

            $csv_row = array(
                $awb,
                $weight,
                $remarks,
                $image_urls
            );

            //get shipment data
            $shipment = $this->CI->shipping_lib->getByAWB($awb);
            if (empty($shipment)) {
                $csv_row[] = 'AWB not found';
                $csv->add_row($csv_row);
                continue;
            }

            $order = $this->CI->orders_lib->getByID($shipment->order_id);

            if (empty($order)) {
                $csv_row[] = 'Order details not found';
                $csv->add_row($csv_row);
                continue;
            }

            $user = $this->CI->user_lib->getByID($shipment->user_id);

            if (empty($user)) {
                $csv_row[] = 'User details not found';
                $csv->add_row($csv_row);
                continue;
            }

            $warehouse = $this->CI->warehouse_lib->getByID($shipment->warehouse_id);

            if (!$warehouse) {
                $csv_row[] = 'Pickup details not found';
                $csv->add_row($csv_row);
                continue;
            }

            $courier = $this->CI->courier_lib->getByID($shipment->courier_id);

            $weight_record = $this->getByShipmentID($shipment->id);

            if (empty($weight_record)) {
                $csv_row[] = 'Weight not uploaded';
                $csv->add_row($csv_row);
                continue;
            }

            if ($weight_record->seller_action_status != 'dispute') {
                $csv_row[] = 'No Dispute Available';
                $csv->add_row($csv_row);
                continue;
            }

            $save = array(
                'seller_action_status' => 'dispute closed',
                'dispute_closure_favour' => $weight
            );

            if ($weight_record->applied_to_wallet == '0') {
                $save['applied_to_wallet'] = '1';
                $save['applied_to_wallet_date'] = time();
            }

            $pricing = $this->getPricing($shipment, $warehouse, $user, $order, @$weight, @$courier_length, @$courier_breadth, @$courier_height);
            $shipping_cost = $pricing->calculateCost();

            if (empty($shipping_cost)) {
                $csv_row[] = 'Unable to calculate freight';
                $csv->add_row($csv_row);
                continue;
            }

            $chargeable_amount = $shipping_cost['courier_charges'];

            $old_shipping_charges = $shipment->courier_fees;

            $new_extra_charges = max(round($chargeable_amount - $old_shipping_charges, 2), 0);

            $additional_weight = round($shipping_cost['calculated_weight'] - $shipment->calculated_weight);

            $save['upload_weight_difference'] = round($additional_weight);
            $save['weight_difference_charges'] = $new_extra_charges;
            $save['weight_new_slab'] = $shipping_cost['calculated_weight'];

            $save_shipment = array(
                'charged_weight' => $shipping_cost['calculated_weight'],
                'extra_weight_charges' => $new_extra_charges,
                'rto_extra_weight_charges' => '0',
            );

            if (strtolower($shipment->ship_status) == 'rto') {
                $save_shipment['rto_extra_weight_charges'] = $new_extra_charges;
            }

            //now calculae the accurate charges to the shipment
            if ($shipment->extra_weight_charges > 0 && $new_extra_charges < $shipment->extra_weight_charges) {
                //revert the amount to seller

                $amount_to_revert = round($shipment->extra_weight_charges - $new_extra_charges, 2);

                if (!$this->revert_weight_charges($shipment, $amount_to_revert)) {
                    $csv_row[] = $this->error;
                    $csv->add_row($csv_row);
                    continue;
                }

                $this->update($weight_record->id, $save);

                $this->close_related_escalation($weight_record->dispute_id, false, $weight, $user_id, $remarks, $image_urls);

                $this->CI->shipping_lib->update($shipment->id, $save_shipment);
                $csv_row[] = 'Success';
                $csv->add_row($csv_row);
                continue;
            }

            if ($shipment->extra_weight_charges > 0 && $new_extra_charges > $shipment->extra_weight_charges) {
                //revert the amount to seller

                $amount_to_deduct = round($new_extra_charges - $shipment->extra_weight_charges, 2);

                $shipment->extra_weight_charges = '0';
                $shipment->rto_extra_weight_charges = '0';

                if (!$this->apply_weight_charges($shipment, $amount_to_deduct)) {
                    $csv_row[] = $this->error;
                    $csv->add_row($csv_row);
                    continue;
                }

                $this->update($weight_record->id, $save);

                $this->close_related_escalation($weight_record->dispute_id, false, $weight, $user_id, $remarks, $image_urls);

                $this->CI->shipping_lib->update($shipment->id, $save_shipment);
                $csv_row[] = 'Success';
                $csv->add_row($csv_row);
                continue;
            }

            if ($weight_record->applied_to_wallet == '0') {
                //release the remittance
                $unhold_amount = $new_extra_charges;
                if (strtolower($shipment->ship_status) == 'rto') {
                    $unhold_amount = round($unhold_amount * 2, 2);
                }

                $this->CI->user_lib->hold_release_remittance($weight_record->user_id, $unhold_amount, 'release');
            }

            if ($new_extra_charges > 0) {
                if (!$this->apply_weight_charges($shipment, $new_extra_charges)) {
                    $csv_row[] = $this->error;
                    $csv->add_row($csv_row);
                    continue;
                }

                $this->update($weight_record->id, $save);
                $this->close_related_escalation($weight_record->dispute_id, false, $weight, $user_id, $remarks, $image_urls);
                $this->CI->shipping_lib->update($shipment->id, $save_shipment);
                $csv_row[] = 'Success';
                $csv->add_row($csv_row);
                continue;
            }

            $this->update($weight_record->id, $save);
            $this->close_related_escalation($weight_record->dispute_id, false, $weight, $user_id, $remarks, $image_urls);
            $this->CI->shipping_lib->update($shipment->id, $save_shipment);

            $csv_row[] = 'Success';
            $csv->add_row($csv_row);
        }

        $csv->export_csv();

        return true;
    }

    private function close_dispute_seller_favour($csvData = array(), $user_id = false)
    {
        foreach ($csvData as $row_key => $row) {
            if (!$this->validate_bulk_import_file_data($row)) {
                $this->error = 'Row no. ' . ($row_key + 1) . $this->error;
                return false;
            }
        }

        $this->CI->load->library('admin/shipping_lib');
        $this->CI->load->library('admin/user_lib');

        // Load CSV reader library

        $csv = new Csv_lib();

        $csv->add_row(array('AWB Number', 'Remarks', 'Image Urls', 'Message'));

        $update = array();
        foreach ($csvData as $row2) {

            $remarks = $image_urls = '';

            $awb = $row2['AWB Number'];

            if (isset($row2['Remarks'])) {
                $remarks = $row2['Remarks'];
            }
            if (isset($row2['Image Urls'])) {
                $image_urls = $row2['Image Urls'];
            }

            $csv_row = array(
                $awb,
                $remarks,
                $image_urls
            );

            //get shipment data
            $shipment = $this->CI->shipping_lib->getByAWB($awb);
            if (empty($shipment)) {
                $csv_row[] = 'AWB not found';
                $csv->add_row($csv_row);
                continue;
            }

            $weight_record = $this->getByShipmentID($shipment->id);

            if (empty($weight_record)) {
                $csv_row[] = 'Weight not uploaded';
                $csv->add_row($csv_row);
                continue;
            }

            if ($weight_record->seller_action_status != 'dispute') {
                $csv_row[] = 'No Dispute Available';
                $csv->add_row($csv_row);
                continue;
            }

            $save = array(
                'seller_action_status' => 'dispute closed',
                'dispute_closure_favour' => 'seller',
                'weight_new_slab' => $weight_record->seller_booking_weight,
                'weight_difference_charges' => '0',
                'upload_weight_difference' => '0'
            );

            if ($weight_record->applied_to_wallet == '0') {

                $unhold_amount = $weight_record->weight_difference_charges;
                if (strtolower($shipment->ship_status) == 'rto') {
                    $unhold_amount = round($unhold_amount * 2, 2);
                }

                //charges not applied to wallet.
                $this->update($weight_record->id, $save);

                $this->close_related_escalation($weight_record->dispute_id, 'seller', false, $user_id, $remarks, $image_urls);
                $this->CI->user_lib->hold_release_remittance($weight_record->user_id, $unhold_amount, 'release');
                $csv_row[] = 'Success';
                $csv->add_row($csv_row);
                continue;
            }

            //charges already applied to wallet. Now need to revert all these

            if (!$this->revert_weight_charges($shipment, $weight_record->weight_difference_charges)) {
                $csv_row[] = $this->error;
                $csv->add_row($csv_row);
                continue;
            }

            $this->update($weight_record->id, $save);

            $this->close_related_escalation($weight_record->dispute_id, 'seller', false, $user_id, $remarks, $image_urls);

            $save_shipment = array(
                'charged_weight' => '0',
            );

            $this->CI->shipping_lib->update($shipment->id, $save_shipment);

            //release hold remittance if any

            $csv_row[] = 'Success';
            $csv->add_row($csv_row);
        }

        $csv->export_csv();

        return true;
    }


    private function close_dispute_courier_favour($csvData = array(), $user_id = false)
    {
        foreach ($csvData as $row_key => $row) {
            if (!$this->validate_bulk_import_file_data($row)) {
                $this->error = 'Row no. ' . ($row_key + 1) . $this->error;
                return false;
            }
        }

        $this->CI->load->library('admin/shipping_lib');
        $this->CI->load->library('admin/user_lib');

        // Load CSV reader library

        $csv = new Csv_lib();

        $csv->add_row(array('AWB Number', 'Remarks', 'Image Urls', 'Message'));

        $update = array();
        foreach ($csvData as $row2) {
            $remarks = $image_urls = '';

            $awb = $row2['AWB Number'];

            if (isset($row2['Remarks'])) {
                $remarks = $row2['Remarks'];
            }
            if (isset($row2['Image Urls'])) {
                $image_urls = $row2['Image Urls'];
            }

            $csv_row = array(
                $awb,
                $remarks,
                $image_urls

            );

            //get shipment data
            $shipment = $this->CI->shipping_lib->getByAWB($awb);
            if (empty($shipment)) {
                $csv_row[] = 'AWB not found';
                $csv->add_row($csv_row);
                continue;
            }

            $weight_record = $this->getByShipmentID($shipment->id);

            if (empty($weight_record)) {
                $csv_row[] = 'Weight not uploaded';
                $csv->add_row($csv_row);
                continue;
            }

            if ($weight_record->seller_action_status != 'dispute') {
                $csv_row[] = 'No Dispute Available';
                $csv->add_row($csv_row);
                continue;
            }

            $save = array(
                'seller_action_status' => 'dispute closed',
                'dispute_closure_favour' => 'courier'
            );

            if ($weight_record->applied_to_wallet == '1') {
                //charges already applied to wallet.
                $this->update($weight_record->id, $save);
                $this->close_related_escalation($weight_record->dispute_id, 'courier', false, $user_id, $remarks, $image_urls);
                $csv_row[] = 'Success';
                $csv->add_row($csv_row);
                continue;
            }

            //charges not applied. Apply to wallet now.

            if (!$this->apply_weight_charges($shipment, $weight_record->weight_difference_charges)) {
                $csv_row[] = $this->error;
                $csv->add_row($csv_row);
                continue;
            }

            $save['applied_to_wallet'] = '1';
            $save['applied_to_wallet_date'] = time();

            // now release any remittance if on hold

            $unhold_amount = $weight_record->weight_difference_charges;
            if (strtolower($shipment->ship_status) == 'rto') {
                $unhold_amount = round($unhold_amount * 2, 2);
            }

            $this->CI->user_lib->hold_release_remittance($weight_record->user_id, $unhold_amount, 'release');

            $this->update($weight_record->id, $save);

            $this->close_related_escalation($weight_record->dispute_id, 'courier', false, $user_id, $remarks, $image_urls);

            $save_shipment = array(
                'charged_weight' => $weight_record->weight_new_slab,
            );

            $this->CI->shipping_lib->update($shipment->id, $save_shipment);

            //release hold remittance if any

            $csv_row[] = 'Success';
            $csv->add_row($csv_row);
        }

        $csv->export_csv();

        return true;
    }

    private function close_related_escalation($dispute_id = false, $won_by = false, $final_weight = false, $user_id = false, $remarks = false, $image_urls = false)
    {
        if (empty($dispute_id))
            return false;

        $message = $remarks;
        if (empty($remarks)) {
            $message = 'Dispute closed in favour of ' . $won_by;
            if ($final_weight)
                $message = 'Dispute closed with final weight ' . $final_weight . 'g';
        }

        $this->CI->load->library('admin/escalation_lib');

        $save = array(
            'remarks' => $message,
            'action_by' => 'delta',
            'status' => 'closed',
            'action_user_id' => $user_id,
            'attachments' => $image_urls,
        );

        $this->CI->escalation_lib->submit_action($dispute_id, $save);
    }

    private function  revert_extra_charges($csvData = array())
    {
        foreach ($csvData as $row_key => $row) {
            if (!$this->validate_bulk_import_file_data($row)) {
                $this->error = 'Row no. ' . ($row_key + 1) . $this->error;
                return false;
            }
        }

        $this->CI->load->library('admin/shipping_lib');

        // Load CSV reader library

        $csv = new Csv_lib();

        $csv->add_row(array('AWB Number', 'Message'));

        $update = array();
        foreach ($csvData as $row2) {
            $awb = $row2['AWB Number'];

            $csv_row = array(
                $awb,
            );

            //get shipment data
            $shipment = $this->CI->shipping_lib->getByAWB($awb);
            if (empty($shipment)) {
                $csv_row[] = 'AWB not found';
                $csv->add_row($csv_row);
                continue;
            }

            $weight_record = $this->getByShipmentID($shipment->id);

            if (empty($weight_record)) {
                $csv_row[] = 'Weight not uploaded';
                $csv->add_row($csv_row);
                continue;
            }

            if ($weight_record->weight_applied == '1') {
                $csv_row[] = 'Weight has been applied';
                $csv->add_row($csv_row);
                continue;
            }

            if ($weight_record->weight_charges_reverted > 0) {
                $csv_row[] = 'Already Done';
                $csv->add_row($csv_row);
                continue;
            }

            $save = array(
                'weight_difference_charges' => '0',
                'weight_charges_reverted' => $weight_record->weight_difference_charges,
            );

            $this->update($weight_record->id, $save);

            $csv_row[] = 'Success';
            $csv->add_row($csv_row);
        }

        $csv->export_csv();

        return true;
    }

    private function apply_weight($csvData = array())
    {
        foreach ($csvData as $row_key => $row) {
            if (!$this->validate_bulk_import_file_data($row)) {
                $this->error = 'Row no. ' . ($row_key + 1) . $this->error;
                return false;
            }
        }

        $this->CI->load->library('admin/shipping_lib');

        // Load CSV reader library

        $csv = new Csv_lib();

        $csv->add_row(array('AWB Number', 'Message'));

        $update = array();
        foreach ($csvData as $row2) {
            $awb = $row2['AWB Number'];

            $csv_row = array(
                $awb,
            );

            //get shipment data
            $shipment = $this->CI->shipping_lib->getByAWB($awb);
            if (empty($shipment)) {
                $csv_row[] = 'AWB not found';
                $csv->add_row($csv_row);
                continue;
            }

            $weight_record = $this->getByShipmentID($shipment->id);

            if (empty($weight_record)) {
                $csv_row[] = 'Weight not uploaded';
                $csv->add_row($csv_row);
                continue;
            }

            if ($weight_record->weight_applied == '1') {
                $csv_row[] = 'Weight already applied';
                $csv->add_row($csv_row);
                continue;
            }

            $save = array(
                'weight_applied' => '1',
                'apply_weight_date' => time(),
            );

            if ($weight_record->weight_difference_charges > 0) {
                $hold_amount = $weight_record->weight_difference_charges;
                if (strtolower($shipment->ship_status) == 'rto') {
                    $hold_amount = round($hold_amount * 2, 2);
                }
                $this->CI->user_lib->hold_release_remittance($weight_record->user_id, $hold_amount, 'hold');
            } else {
                //amount is less than zero so apply automatically
                $save['applied_to_wallet'] = '1';
                $save['applied_to_wallet_date'] = time();
            }

            $this->update($weight_record->id, $save);

            $csv_row[] = 'Success';
            $csv->add_row($csv_row);
        }

        $csv->export_csv();

        return true;
    }

    private function  charge_to_wallet($csvData = array())
    {
        foreach ($csvData as $row_key => $row) {
            if (!$this->validate_bulk_import_file_data($row)) {
                $this->error = 'Row no. ' . ($row_key + 1) . $this->error;
                return false;
            }
        }

        $this->CI->load->library('admin/shipping_lib');
        $this->CI->load->library('admin/user_lib');

        // Load CSV reader library

        $csv = new Csv_lib();

        $csv->add_row(array('AWB Number', 'Message'));

        $update = array();
        foreach ($csvData as $row2) {
            $awb = $row2['AWB Number'];

            $csv_row = array(
                $awb,
            );

            //get shipment data
            $shipment = $this->CI->shipping_lib->getByAWB($awb);
            if (empty($shipment)) {
                $csv_row[] = 'AWB not found';
                $csv->add_row($csv_row);
                continue;
            }

            $weight_record = $this->getByShipmentID($shipment->id);

            if (empty($weight_record)) {
                $csv_row[] = 'Weight not uploaded';
                $csv->add_row($csv_row);
                continue;
            }

            if ($weight_record->applied_to_wallet == '1') {
                $csv_row[] = 'Charges already applied';
                $csv->add_row($csv_row);
                continue;
            }

            $save = array(
                'applied_to_wallet' => '1',
                'applied_to_wallet_date' => time(),
            );

            if ($weight_record->weight_applied == '0') {
                $save['weight_applied'] = '1';
                $save['apply_weight_date'] = time();
            }

            if ($weight_record->weight_difference_charges <= 0) {
                $this->update($weight_record->id, $save);
                $csv_row[] = 'Success';
                $csv->add_row($csv_row);
                continue;
            }

            if (!$this->apply_weight_charges($shipment, $weight_record->weight_difference_charges)) {
                $csv_row[] = $this->error;
                $csv->add_row($csv_row);
                continue;
            }

            if ($weight_record->weight_applied == '1') { // applied earlier so release remittance
                $unhold_amount = $weight_record->weight_difference_charges;
                if (strtolower($shipment->ship_status) == 'rto') {
                    $unhold_amount = round($unhold_amount * 2, 2);
                }

                $this->CI->user_lib->hold_release_remittance($weight_record->user_id, $unhold_amount, 'release');
            }

            $this->update($weight_record->id, $save);

            $save_shipment = array(
                'charged_weight' => $weight_record->weight_new_slab,
            );

            $this->CI->shipping_lib->update($shipment->id, $save_shipment);

            //release hold remittance if any

            $csv_row[] = 'Success';
            $csv->add_row($csv_row);
        }

        $csv->export_csv();

        return true;
    }

    private function apply_weight_charges(object $shipment = NULL, $amount = 0)
    {
        if (empty($shipment->id) || empty($amount))
            return false;

        if ($shipment->extra_weight_charges > 0) {
            $this->error = 'Charges already applied';
            return false;
        }

        $this->CI->load->library('admin/shipping_lib');
        $this->CI->load->library('wallet_lib');

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

        //charge balance from customer wallet
        $wallet = new Wallet_lib();
        $wallet->setUserID($shipment->user_id);
        $wallet->setAmount($amount);
        $wallet->setTransactionType('debit');
        $wallet->setNotes('Extra Weight Charges Applied');
        $wallet->setTxnFor('shipment');
        $wallet->setRefID($shipment->id);
        $wallet->setTxnRef('extra_weight');

        if (!$wallet->creditDebitWallet())
            return false;

        $save = array(
            'extra_weight_charges' => $amount
        );

        if (($plan_type != 'per_dispatch') && (strtolower($shipment->ship_status) == 'rto') && ($shipment->rto_extra_weight_charges <= 0)) {
            $wallet = new Wallet_lib();
            $wallet->setUserID($shipment->user_id);
            $wallet->setAmount($amount);
            $wallet->setTransactionType('debit');
            $wallet->setNotes('RTO Extra Weight Charges Applied');
            $wallet->setTxnFor('shipment');
            $wallet->setRefID($shipment->id);
            $wallet->setTxnRef('rto_extra_weight');

            if (!$wallet->creditDebitWallet())
                return false;

            $save['rto_extra_weight_charges'] = $amount;
        }

        $this->CI->shipping_lib->update($shipment->id, $save);

        return true;
    }

    private function revert_weight_charges(object $shipment = NULL, $amount = 0)
    {
        if (empty($shipment->id) || empty($amount))
            return false;

        if ($shipment->extra_weight_charges <= 0) {
            $this->error = 'No deduction found';
            return false;
        }


        if (!$amount)
            $amount = $shipment->extra_weight_charges;

        $this->CI->load->library('admin/shipping_lib');
        $this->CI->load->library('wallet_lib');


        //charge balance from customer wallet
        $wallet = new Wallet_lib();
        $wallet->setUserID($shipment->user_id);
        $wallet->setAmount($amount);
        $wallet->setTransactionType('credit');
        $wallet->setNotes('Extra Weight Charges Refunded');
        $wallet->setTxnFor('shipment');
        $wallet->setRefID($shipment->id);
        $wallet->setTxnRef('extra_weight');

        if (!$wallet->creditDebitWallet())
            return false;

        $save = array(
            'extra_weight_charges' => '0'
        );

        if (strtolower($shipment->ship_status) == 'rto' && $shipment->rto_extra_weight_charges > 0) {

            if (!$amount)
                $amount = $shipment->rto_extra_weight_charges;

            $wallet = new Wallet_lib();
            $wallet->setUserID($shipment->user_id);
            $wallet->setAmount($amount);
            $wallet->setTransactionType('credit');
            $wallet->setNotes('RTO Extra Weight Charges Refunded');
            $wallet->setTxnFor('shipment');
            $wallet->setRefID($shipment->id);
            $wallet->setTxnRef('rto_extra_weight');

            if (!$wallet->creditDebitWallet())
                return false;

            $save['rto_extra_weight_charges'] = '0';
        }

        $this->CI->shipping_lib->update($shipment->id, $save);

        return true;
    }

    private function validate_bulk_import_file_data($data)
    {
        $this->CI->form_validation->set_data($data);

        $this->CI->form_validation->set_message('required', '%s is required');
        $this->CI->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');

        $config = array(
            array(
                'field' => 'AWB Number',
                'label' => 'AWB Number',
                'rules' => 'trim|required|alpha_numeric|min_length[4]|max_length[20]',
            )
        );

        $this->CI->form_validation->set_rules($config);

        if ($this->CI->form_validation->run()) {
            return true;
        } else {
            $this->error = validation_errors();
            return false;
        }
    }

    private function validate_new_weight_file_data($data)
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
                'field' => 'Weight',
                'label' => 'Weight',
                'rules' => 'trim|required|numeric|greater_than_equal_to[500]',
            )
        );

        $this->CI->form_validation->set_rules($config);

        if ($this->CI->form_validation->run()) {
            return true;
        } else {
            $this->error = validation_errors();
            return false;
        }
    }

    private function getPricing($shipment, $warehouse, $user, $order, $courier_billed_weight, $courier_length, $courier_breadth, $courier_height)
    {
        $order_type = strtolower(isset($shipment->order_type) ? $shipment->order_type : "");
        if ($order_type == 'international') {
            //calculate weight difference for international charges here
            $this->CI->load->library('country_lib');
            $pickup_country = isset($warehouse->country) ? $warehouse->country : "";
            $this->CI->load->library('country_lib');
            $pickp_country_details = $this->CI->country_lib->getCountry($pickup_country);
            $pickup_iso = isset($pickp_country_details->iso) ? $pickp_country_details->iso : "";
            $shipping_country_details = $this->CI->country_lib->getCountry($order->shipping_country);
            $shipping_iso = isset($shipping_country_details->iso) ? $shipping_country_details->iso : "";

            $pricing = new International_pricing_lib();
            $pricing->setPlan($user->international_pricing_plan);
            $pricing->setCourier($shipment->courier_id);
            $pricing->setAmount($order->order_amount);
            $pricing->setWeight($courier_billed_weight);
            $pricing->setLength($courier_length);
            $pricing->setBreadth($courier_breadth);
            $pricing->setHeight($courier_height);

            if (strtolower($order->order_payment_type) == 'reverse') {
                $pricing->setOrigin($pickup_iso);
                $pricing->setDestination($shipping_iso);
                $pricing->setType('reverse');
            } else {
                $pricing->setOrigin($pickup_iso);
                $pricing->setDestination($shipping_iso);
                $pricing->setType($order->order_payment_type);
            }
        } else if($order_type == 'cargo') {
            //calculate Weight difference for b2b charge here
            $order_id_info = array(
                "origin" => $warehouse->zip,
                "destination" => $order->shipping_zip,
                "weight" => $courier_billed_weight/1000,
                "length" => $courier_length,
                "height" => $courier_height,
                "breadth" => $courier_breadth,
                "cod_amount" => $order->order_amount
            );
            $this->CI->load->library('cargo_pricing_lib');
            $pricing = new Cargo_pricing_lib();
            $pricing->setPlan($user->cargo_pricing_plan);
            $pricing->setCourier($shipment->courier_id);
            $pricing->setOrderId($order_id_info);
            $pricing->setOrigin($warehouse->zip);
            $pricing->setDestination($order->shipping_zip);
            $pricing->setType($order->order_payment_type);
            $pricing->setUserID($order->user_id);
        } else {
            //calculate weight difference for domestic charges here
            $pricing = new Pricing_lib();
            $pricing->setPlan($user->pricing_plan);
            $pricing->setCourier($shipment->courier_id);
            $pricing->setBaseFreight($shipment->base_freight);
            $pricing->setRtoFreight($shipment->base_rto_freight);
            $pricing->setExtraFreight($shipment->base_add_weight_freight);
            if (strtolower($order->order_payment_type) == 'reverse') {
                $pricing->setOrigin($order->shipping_zip);
                $pricing->setDestination($warehouse->zip);
                $pricing->setType('reverse');
            } else {
                $pricing->setOrigin($warehouse->zip);
                $pricing->setDestination($order->shipping_zip);
            }
            $pricing->setWeight($courier_billed_weight);
        }
        return $pricing;
    }
}