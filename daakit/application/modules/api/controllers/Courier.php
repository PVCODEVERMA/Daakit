<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';

class Courier extends RestController
{
    var $account_id = false;

    public function __construct()
    {
        parent::__construct('rest_api');

        $this->validateAPIToken();
    }

    private function validateAPIToken()
    {
        $this->load->library('jwt_lib');

        try {
            $api_data = $this->jwt_lib->validateAPI();

            if ($api_data->parent_id == '0')
                $this->account_id = $api_data->user_id;
            else
                $this->account_id = $api_data->parent_id;
        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'message' => $e->getMessage(),
            ], 401);
        }
    }
    function courier_pincode_serviceability_post()
    {
        $input_json = $this->input->raw_input_stream;
    
        $input_data = json_decode($input_json, true);
    
        $this->form_validation->set_data($input_data);
    
        $this->form_validation->set_message('required', '%s is required');
        $this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');
    
    
        $config []= array(
                'field' => 'filterType',
                'label' => 'Filter Type',
                'rules' => 'trim|required|strtolower|in_list[courier,pincode,rate]',
        );
    
        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run() && !empty(validation_errors())) {
            $this->response([
                'status' => false,
                'message' => strip_tags(validation_errors())
            ], 404);
        }
        if(!empty($input_data['filterType']) && $input_data['filterType']=='courier')
        {
            $this->courier();
        }
        if(!empty($input_data['filterType']) && $input_data['filterType']=='pincode')
        {
            $this->pincodeServiceability();
        }
        if(!empty($input_data['filterType']) && $input_data['filterType']=='rate')
        {
            $this->serviceability();
        }
    }
    function courier()
    {
        $return = array();
        $this->load->library('courier_lib');
        $results = $this->courier_lib->userAvailableCouriers($this->account_id);
        if (!empty($results)) {
            foreach ($results as $result) {
                $return[] = array(
                    'id' => $result->id,
                    'name' => $result->name
                );
            }
        }
        $this->response([
            'status' => true,
            'data' => $return
        ], 200);
    }

    function pincodeServiceability()
    {
        $user_id = $this->account_id;
        $this->load->library('courier_lib');
        $user_couriers = $this->courier_lib->userAvailableCouriers($user_id);
        if (!$user_couriers)
            return false;

        $user_courier_ids = array_keys($user_couriers);

        $this->load->library('pincode_lib');
        $pincode_list = $this->pincode_lib->get_user_pincodes_list($user_courier_ids);

        $this->response([
            'status' => true,
            'count' => count($pincode_list),
            'data' => $pincode_list
        ], 200);
    }
    function serviceability()
    {
        $input_json = $this->input->raw_input_stream;

        $input_data = json_decode($input_json, true);


        $this->form_validation->set_data($input_data);

        $this->form_validation->set_message('required', '%s is required');
        $this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');


        $config = array(
            array(
                'field' => 'origin',
                'label' => 'Origin',
                'rules' => 'trim|required|integer|exact_length[6]',
            ),
            array(
                'field' => 'destination',
                'label' => 'Destination',
                'rules' => 'trim|required|integer|exact_length[6]',
            ),
            array(
                'field' => 'paymentType',
                'label' => 'Payment Type',
                'rules' => 'trim|required|in_list[cod,prepaid]',
            ),
            array(
                'field' => 'weight',
                'label' => 'Package Weight',
                'rules' => 'trim|integer|greater_than_equal_to[0]',
            ),
            array(
                'field' => 'length',
                'label' => 'Package Length',
                'rules' => 'trim|integer|greater_than_equal_to[0]',
            ),
            array(
                'field' => 'breadth',
                'label' => 'Package Breadth',
                'rules' => 'trim|integer|greater_than_equal_to[0]',
            ),
            array(
                'field' => 'height',
                'label' => 'Package Height',
                'rules' => 'trim|integer|greater_than_equal_to[0]',
            ),
        );

        if (!empty($input_data['paymentType']) && $input_data['paymentType'] == 'cod') {
            $config[] = array(
                'field' => 'orderAmount',
                'label' => 'Order Amount',
                'rules' => 'trim|required|numeric|greater_than[0]',
            );
        } else {
            $config[] = array(
                'field' => 'orderAmount',
                'label' => 'Order Amount',
                'rules' => 'trim|numeric|greater_than[0]',
            );
        }


        $this->form_validation->set_rules($config);

        if (!$this->form_validation->run() && !empty(validation_errors())) {
            $this->response([
                'status' => false,
                'message' => strip_tags(validation_errors())
            ], 404);
        }

        $this->load->library('user_lib');
        $user = $this->user_lib->getByID($this->account_id);

        $origin = $input_data['origin'];
        $destination = $input_data['destination'];
        $weight = (!empty($input_data['weight'])) ? $input_data['weight'] : '500';
        $length = (!empty($input_data['length'])) ? $input_data['length'] : '10';
        $height = (!empty($input_data['breadth'])) ? $input_data['breadth'] : '10';
        $breadth = (!empty($input_data['height'])) ? $input_data['height'] : '10';
        $payment_type = strtolower($input_data['paymentType']);

        $order_amount = (!empty($input_data['orderAmount'])) ? $input_data['orderAmount'] : '0';

        $this->load->library('pincode_lib');
        $this->load->library('courier_lib');
        $user_couriers = $this->courier_lib->userAvailableCouriers($this->account_id);

        $servicalbe_couriers = array();
        //check pickup service
        $pickups_couriers = $this->pincode_lib->getPickupService($origin);

        if (empty($pickups_couriers)) {
            $this->response([
                'status' => true,
                'data' => $servicalbe_couriers,
            ], 200);
        }

        $pickup_courier_list = array();
        foreach ($pickups_couriers as $pickups_courier) {
            $pickup_courier_list[$pickups_courier->id] = $pickups_courier;
        }


        //check delivery pincode serviceblity
        $delivery_couriers = $this->pincode_lib->getPincodeService($destination, $payment_type);

        if (!empty($delivery_couriers)) { //get courier price
            foreach ($delivery_couriers as $c_key => $courier) {
                if (!array_key_exists($courier->id, $pickup_courier_list)) {
                    unset($delivery_couriers[$c_key]);
                }

                if (!array_key_exists($courier->id, $user_couriers))
                    unset($delivery_couriers[$c_key]);

                $pricing = new Pricing_lib();
                $pricing->setPlan($user->pricing_plan);
                $pricing->setCourier($courier->id);
                $pricing->setOrigin($origin);
                $pricing->setDestination($destination);
                $pricing->setType($payment_type);
                $pricing->setAmount($order_amount);
                $pricing->setWeight($weight);
                $pricing->setLength($length);
                $pricing->setBreadth($breadth);
                $pricing->setHeight($height);

                $shipping_cost = $pricing->calculateCost();
                if (!empty($shipping_cost)) {
                    $courier->charges = (object)$shipping_cost;
                }
            }
        }


        //pr($delivery_couriers, true);

        //pr($delivery_couriers, true);
        if (empty($delivery_couriers)) {
            $this->response([
                'status' => true,
                'data' => $servicalbe_couriers,
            ], 200);
        }

        foreach ($delivery_couriers as $c) {
            $servicalbe_couriers[] = array(
                'id' => $c->id,
                'name' => $c->name,
                'freightCharges' => $c->charges->courier_charges,
                'codCharges' => $c->charges->cod_charges,
                'totalCharges' => $c->charges->total,
                'minWeight' => (int)$c->weight,
                'chargeableWeight' =>  (int)$c->charges->calculated_weight,
            );
        }

        $this->response([
            'status' => true,
            'data' => $servicalbe_couriers,
        ], 200);
    }
}
