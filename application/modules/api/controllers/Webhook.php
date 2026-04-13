<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';


class Webhook1 extends RestController
{

    var $account_id = false;

    public function __construct()
    {

        parent::__construct('rest_api');

        $this->validateAPIToken();
        $this->load->library('webhook_lib');
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

    function create_post()
    {
        $input_json = $this->input->raw_input_stream;
        $input_data = json_decode($input_json, true);
        $this->form_validation->set_data($input_data);
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'name',
                'label' => 'Webhook Name',
                'rules' => 'trim|required|min_length[2]|max_length[20]|alpha_numeric_spaces'
            ),
            array(
                'field' => 'url',
                'label' => 'Webhook URL',
                'rules' => 'trim|required|min_length[2]|max_length[200]|callback_validate_url'
            ),
            array(
                'field' => 'secret',
                'label' => 'Webhook Secret',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'status',
                'label' => 'Status',
                'rules' => 'trim|required|in_list[active,paused]'
            ),
        );

        $this->form_validation->set_rules($config);

        if (!$this->form_validation->run()) {
            $this->response([
                'status' => false,
                'message' => strip_tags(validation_errors())
            ], 404);
        }
        $status = '0';

        if ($input_data['status'] == 'active') {
            $status = '1';
        }

        $save = array(
            'user_id' => $this->account_id,
            'name' =>  isset($input_data['name']) ? $input_data['name'] : '',
            'url' => isset($input_data['url']) ? $input_data['url'] : '',
            'secret' => isset($input_data['secret']) ? $input_data['secret'] : '',
            'status' => $status
        );

        $webhookid = $this->webhook_lib->create($save);

        $message = "Webhook Created";

        $this->response([
            'status' => true,
            'data' => $webhookid
        ], 200);
    }

    function update_post($id = false)
    {
        if (empty($id)) {
            $this->response([
                'status' => false,
                'message' => 'The Webhook id must be required'
            ], 404);
        }
        
        if (!is_numeric($id)) {
            $this->response([
                'status' => false,
                'message' => 'The Webhook id must be numeric'
            ], 404);
        }

        $input_json = $this->input->raw_input_stream;
        $input_data = json_decode($input_json, true);
        $this->form_validation->set_data($input_data);
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'name',
                'label' => 'Webhook Name',
                'rules' => 'trim|required|min_length[2]|max_length[20]|alpha_numeric_spaces'
            ),
            array(
                'field' => 'url',
                'label' => 'Webhook URL',
                'rules' => "trim|required|min_length[2]|max_length[200]|callback_validate_url[" .$id. "]"
            ),
            array(
                'field' => 'secret',
                'label' => 'Webhook Secret',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'status',
                'label' => 'Status',
                'rules' => 'trim|required|in_list[active,paused]'
            ),
        );

        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->response([
                'status' => false,
                'message' => strip_tags(validation_errors())
            ], 404);
        }
        $status = '0';

        if ($input_data['status'] == 'active') {
            $status = '1';
        }

        $save = array(
            'user_id' => $this->account_id,
            'name' =>  isset($input_data['name']) ? $input_data['name'] : '',
            'url' => isset($input_data['url']) ? $input_data['url'] : '',
            'secret' => isset($input_data['secret']) ? $input_data['secret'] : '',
            'status' => $status
        );

        $check = $this->webhook_lib->getUserAPIWebhooksID($id);

        if (empty($check) || $check->user_id != $this->account_id) {
            $this->response([
                'status' => false,
                'message' => 'No records found'
            ], 404);
        }

        $this->webhook_lib->update($id, $save);

        $this->response([
            'status' => true,
            'data' => $id
        ], 200);
    }
    public function validate_url($url, $id = false)
    {
        if (!preg_match('/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}' . '((:[0-9]{1,5})?\\/.*)?$/i', $url)) {
            $this->form_validation->set_message('validate_url', 'Please enter a valid URL in format http(s)://website.com ');
            return false;
        } else {


            $checklist = $this->webhook_lib->getUserAPIWebhooks($url, $this->account_id, $id);
            if (!empty($checklist)) {
                $this->form_validation->set_message('validate_url', 'Url already exists');
                return false;
            }
            return true;
        }
    }
}
