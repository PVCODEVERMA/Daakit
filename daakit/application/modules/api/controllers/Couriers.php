<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';

class couriers11 extends RestController {

    var $account_id = false;

    public function __construct() {
        parent::__construct('rest_api');
        $this->methods['index_get']['limit'] = 10;
        if (!empty($this->_apiuser))
            $this->account_id = $this->_apiuser->user_id;
        $this->load->library('courier_lib');
    }

    //get all orders from the db. Default is 50
    function index_get() {

        $return = array();
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

}
