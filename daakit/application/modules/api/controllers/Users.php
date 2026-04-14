<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';

class Users extends RestController
{

    var $account_id = false;

    public function __construct()
    {
        parent::__construct('rest_api');

        $this->load->library('user_lib');
    }

    function token_post()
    {
        $input_json = $this->input->raw_input_stream;

        $input_data = json_decode($input_json, true);

        $this->form_validation->set_data($input_data);

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'user_name',
                'label' => 'User Name',
                'rules' => 'trim|required|min_length[3]|max_length[200]|valid_email'
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|required|min_length[6]|max_length[50]'
            )
        );

        $this->form_validation->set_rules($config);

        if (!$this->form_validation->run()) {
            $this->response([
                'status' => false,
                'message' => strip_tags(validation_errors())
            ], 404);
        }

        $token = $this->user_lib->userApiLogin($input_data['user_name'], $input_data['password']);
        if (!$token) {
            $this->response([
                'status' => false,
                'message' => $this->user_lib->get_error(),
            ], 401);
        }

        $this->response([
            'status' => true,
            'data' => $token
        ], 200);
    }

    function authToken1_post()
    {
        $input_json = $this->input->raw_input_stream;

        $input_data = json_decode($input_json, true);

        $this->form_validation->set_data($input_data);

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'username',
                'label' => 'Username',
                'rules' => 'trim|required|min_length[3]|max_length[200]|valid_email'
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|required|min_length[6]|max_length[50]'
            )
        );

        $this->form_validation->set_rules($config);

        if (!$this->form_validation->run()) {
            $this->response([
                'status' => "INVALID_CREDENTIALS",
                'message' => strip_tags(validation_errors())
            ], 404);
        }

        $token = $this->user_lib->userApiLogin($input_data['username'], $input_data['password']);
        if (!$token) {
            $this->response([
                'status' => "INVALID_CREDENTIALS",
                'message' => $this->user_lib->get_error(),
            ], 401);
        }

        $this->response([
            'status' => 'SUCCESS',
            'token' => $token
        ], 200);
    }
   
}
