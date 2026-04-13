<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pincode_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('admin/pincode_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->pincode_model, $method)) {
            throw new Exception('Undefined method pincode_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->pincode_model, $method], $arguments);
    }

    function uploadPincodes()
    {

        $this->CI->load->library('form_validation');
        $config = array(
            array(
                'field' => 'courier_id',
                'label' => 'Courier',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'action',
                'label' => 'Action',
                'rules' => 'trim|required|in_list[replace,update]',
            ),

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
                if (!$this->validate_upload_data($this->CI->input->post('courier_id'), $row)) {
                    $this->error = 'Row no. ' . ($row_key + 1) . $this->error;
                    return false;
                }
            }

            $courier_id = $this->CI->input->post('courier_id');
            $action = $this->CI->input->post('action');

            if ($action == 'replace') {
                //delete all existing pincodes for this courier
                $this->deleteCourierPincodes($courier_id);

                $save = array();
                foreach ($csvData as $row_key => $row) {
                    $s = array(
                        'pincode' => $row['Pincode'],
                        'courier_id' => $courier_id,
                        'city' => (!empty($row['City'])) ? $row['City'] : '',
                        'state_code' => (!empty($row['State'])) ? $row['State'] : '',
                        'cod' => (!empty($row['COD'])) ? strtoupper($row['COD']) : 'N',
                        'prepaid' => (!empty($row['Prepaid'])) ? strtoupper($row['Prepaid']) : 'N',
                        'pickup' => (!empty($row['Pickup'])) ? strtoupper($row['Pickup']) : 'N',
                        'area_code' => (!empty($row['Area Code'])) ? strtoupper($row['Area Code']) : '',
                        'sdd_code' => (!empty($row['SDD Code'])) ? strtoupper($row['SDD Code']) : '',
                        'is_reverse_pickup' => (!empty($row['Reverse Pickup'])) ? strtoupper($row['Reverse Pickup']) : 'N',
                    );
                    $save[] = $s;
                }

                $this->batchInsert($save);

                return true;
            } else {
                //update existing records
                $existing = $this->getCourierPincodes($courier_id);

                $existing_records = array();
                if (!empty($existing)) {
                    foreach ($existing as $ext) {
                        $existing_records[$ext->pincode] = $ext;
                    }
                }


                $insert = array();
                $update = array();
                foreach ($csvData as $row_key => $row) {
                    if (array_key_exists($row['Pincode'], $existing_records)) {
                        $update[] = array(
                            'id' => $existing_records[$row['Pincode']]->id,
                            'city' => (!empty($row['City'])) ? $row['City'] : $existing_records[$row['Pincode']]->city,
                            'state_code' => (!empty($row['State'])) ? $row['State'] : $existing_records[$row['Pincode']]->state_code,
                            'cod' => (!empty($row['COD'])) ? strtoupper($row['COD']) : $existing_records[$row['Pincode']]->cod,
                            'prepaid' => (!empty($row['Prepaid'])) ? strtoupper($row['Prepaid']) : $existing_records[$row['Pincode']]->prepaid,
                            'pickup' => (!empty($row['Pickup'])) ? strtoupper($row['Pickup']) : $existing_records[$row['Pincode']]->pickup,
                            'area_code' => (!empty($row['Area Code'])) ? strtoupper($row['Area Code']) : $existing_records[$row['Pincode']]->area_code,
                            'sdd_code' => (!empty($row['SDD Code'])) ? strtoupper($row['SDD Code']) : '',
                            'is_reverse_pickup' => (!empty($row['Reverse Pickup'])) ? strtoupper($row['Reverse Pickup']) : $existing_records[$row['Pincode']]->is_reverse_pickup,
                        );
                    } else {
                        $insert[] = array(
                            'pincode' => $row['Pincode'],
                            'courier_id' => $courier_id,
                            'city' => (!empty($row['City'])) ? $row['City'] : '',
                            'state_code' => (!empty($row['State'])) ? $row['State'] : '',
                            'cod' => (!empty($row['COD'])) ? strtoupper($row['COD']) : 'N',
                            'prepaid' => (!empty($row['Prepaid'])) ? strtoupper($row['Prepaid']) : 'N',
                            'pickup' => (!empty($row['Pickup'])) ? strtoupper($row['Pickup']) : 'N',
                            'area_code' => (!empty($row['Area Code'])) ? strtoupper($row['Area Code']) : '',
                            'sdd_code' => (!empty($row['SDD Code'])) ? strtoupper($row['SDD Code']) : '',
                            'is_reverse_pickup' => (!empty($row['Reverse Pickup'])) ? strtoupper($row['Reverse Pickup']) : 'N',
                        );
                    }
                }

                if (!empty($insert))
                    $this->batchInsert($insert);
                if (!empty($update))
                    $this->batchUpdate($update);

                return true;
            }
        }


        return true;
    }


    private function validate_upload_data($courier_id = false, $data = false)
    {
        $this->CI->form_validation->set_data($data);

        $this->CI->form_validation->set_message('required', '%s is required');
        $this->CI->form_validation->set_message('alpha_numeric', 'Only Characters & Numbers are allowed in %s');
        $this->CI->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');

        $config = array(
            array(
                'field' => 'Pincode',
                'label' => 'Pincode',
                'rules' => 'trim|required|numeric|exact_length[6]',
            ),
            array(
                'field' => 'City',
                'label' => 'City',
                'rules' => 'trim',
            ),
            array(
                'field' => 'State',
                'label' => 'State',
                'rules' => 'trim',
            ),
            array(
                'field' => 'COD',
                'label' => 'COD',
                'rules' => 'trim|in_list[Y,N,y,n]',
            ),
            array(
                'field' => 'Prepaid',
                'label' => 'Prepaid',
                'rules' => 'trim|in_list[Y,N,y,n]',
            ),
            array(
                'field' => 'Pickup',
                'label' => 'Pickup',
                'rules' => 'trim|in_list[Y,N,y,n]',
            ),

            array(
                'field' => 'Reverse Pickup',
                'label' => 'Reverse Pickup',
                'rules' => 'trim|in_list[Y,N,y,n]',
            ),

        );

        $are_code_required = [];

        if (in_array($courier_id, $are_code_required)) {
            $config[] = array(
                'field' => 'Area Code',
                'label' => 'Area Code',
                'rules' => 'trim|required',
            );
        } else {
            $config[] = array(
                'field' => 'Area Code',
                'label' => 'Area Code',
                'rules' => 'trim',
            );
        }



        $this->CI->form_validation->set_rules($config);

        if ($this->CI->form_validation->run()) {
            return true;
        } else {
            $this->error = validation_errors();
            return false;
        }
    }
}
