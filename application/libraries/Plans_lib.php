<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Plans_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();
        $this->CI->load->model('plans_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->plans_model, $method)) {
            throw new Exception('Undefined method plans_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->plans_model, $method], $arguments);
    }

    
    function uploadLandingPrice()
    {
        $this->CI->load->library('form_validation');
        $config = array(
            array(
                'field' => 'action',
                'label' => 'Action',
                'rules' => 'trim|required|in_list[replace]',
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
            $this->CI->load->library('csvreader');

            $csvData = $this->CI->csvreader->parse_csv($_FILES['importFile']['tmp_name']);

            if (empty($csvData)) {
                $this->error = 'Blank CSV File';
                return false;
            }

            foreach ($csvData as $row_key => $row) {
                if (!$this->validate_upload_data($row)) {
                    $this->error = 'Row no. ' . ($row_key + 1) . $this->error;
                    return false;
                }
            }

            foreach ($csvData as $row_key => $row_value) {

                $gstMultiplier = 1.18;

                $fields = ['zone1','zone2','zone3','zone4','zone5','min_cod'];

                foreach ($fields as $field) {
                    $row_value[$field] = round($row_value[$field] * $gstMultiplier, 2);
                }

                $row_value['cod_percent'] = round($row_value['cod_percent'], 2);

                $landing = $this->getLandingPrice(
                    $row_value['courier_id'],
                    $row_value['type']
                );

                if (!$landing) {
                    continue;
                }

                foreach ($fields as $field) {
                    $csvValue = isset($row_value[$field]) ? (float)$row_value[$field] : 0;
                    $landingValue = isset($landing->$field) ? (float)$landing->$field : 0;

                    $row_value[$field] = round($csvValue - $landingValue, 2);
                }

                $csvCod = (float)$row_value['cod_percent'];
                $landingCod = (float)$landing->cod_percent;

                $row_value['cod_percent'] = round($csvCod - $landingCod, 2);

                if ($row = $this->getPlanDetailsByCourierAndType(
                    $row_value['plan_id'],
                    $row_value['courier_id'],
                    $row_value['type']
                )) {
                    $this->updatePrice($row->id, $row_value);
                } else {
                    $this->createPrice($row_value);
                }
            }

            return true;
        }

        return true;
    }

 function validatePricingOnly(){
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

        $this->CI->load->library('csvreader');
        $csvData = $this->CI->csvreader->parse_csv($_FILES['importFile']['tmp_name']);

        if (empty($csvData)) {
            $this->error = 'Blank CSV File';
            return false;
        }

        foreach ($csvData as $row_key => $row) {
                if (!$this->validate_upload_data($row)) {
                    $this->error = 'Validation ERROR Row no. ' . ($row_key + 1) . $this->error;
                    return false;
                }
            }

        $errors = [];
        $gstMultiplier = 1.18;

        foreach ($csvData as $row_key => $row_value) {
            $row_value['zone1'] = round($row_value['zone1'] * $gstMultiplier, 2);
            $row_value['zone2'] = round($row_value['zone2'] * $gstMultiplier, 2);
            $row_value['zone3'] = round($row_value['zone3'] * $gstMultiplier, 2);
            $row_value['zone4'] = round($row_value['zone4'] * $gstMultiplier, 2);
            $row_value['zone5'] = round($row_value['zone5'] * $gstMultiplier, 2);
            $row_value['min_cod'] = round($row_value['min_cod'] * $gstMultiplier, 2);

            $row_value['cod_percent'] = round($row_value['cod_percent'], 2);

            $landing = $this->getLandingPrice(
                $row_value['courier_id'],
                $row_value['type']
            );

            if (!$landing) {
                $errors[] = "Row " . ($row_key + 1) .
                    ": Landing price not found for Courier {$row_value['courier_id']} and Type {$row_value['type']}";
                continue;
            }

            $fields = ['zone1','zone2','zone3','zone4','zone5','min_cod','cod_percent'];

            foreach ($fields as $field) {
                $csvValue = isset($row_value[$field]) ? (float)$row_value[$field] : 0;
                $landingValue = isset($landing->$field) ? (float)$landing->$field : 0;
                if ((float)$row_value[$field] < (float)$landing->$field) {
                    $percentage = $landingValue != 0 ? round(($csvValue / $landingValue) * 100, 2) : 0;
                    $errors[] = "LOW PRICING WARNING Row " . ($row_key + 1) .
                        " | Plan: {$row_value['plan_id']} | Courier: {$row_value['courier_id']} | Type: {$row_value['type']} | {$field} exceeds after GST (CSV: {$row_value[$field]} > Landing: {$landing->$field}) | Percentage: {$percentage}%";
                }
            }
        }

        if (!empty($errors)) {
            $this->error = implode("<br>", $errors);
            return false;
        }

        return true;
    }

    return false;
}

    private function validate_upload_data($data = false)
    {
        $this->CI->form_validation->set_data($data);

        $this->CI->form_validation->set_message('required', '%s is required');
        $this->CI->form_validation->set_message('alpha', 'Only Characters are allowed in %s');

        $config = array(
            array(
                'field' => 'plan_id',
                'label' => 'Plan Id',
                'rules' => 'trim|required|integer|greater_than[0]',
            ),
            array(
                'field' => 'courier_id',
                'label' => 'Courier Id',
                'rules' => 'trim|required|integer|greater_than[0]',
            ),
            array(
                'field' => 'type',
                'label' => 'Type',
                'rules' => 'trim|required|in_list[fwd,rto,weight]',
            ),
            array(
                'field' => 'zone1',
                'label' => 'Zone 1',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'zone2',
                'label' => 'Zone 2',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'zone3',
                'label' => 'Zone 3',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'zone4',
                'label' => 'Zone 4',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'zone5',
                'label' => 'Zone 5',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'min_cod',
                'label' => 'Min COD',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'cod_percent',
                'label' => 'COD Percent',
                'rules' => 'trim|numeric',
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
}
