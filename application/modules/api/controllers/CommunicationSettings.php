<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';

use Firebase\JWT\JWT;

class CommunicationSettings extends RestController
{
    private $jwt_key;

    public function __construct()
    {
        parent::__construct('rest_api');
        $this->load->database();
        $this->jwt_key = base64_decode($this->config->item('jwt_key')); // JWT secret key
    }

    // 1. Save or Update Preferences
    public function saveSettings_post()
    {
        $input_json = $this->input->raw_input_stream;
        $data = json_decode($input_json, true);

        // Get token from Authorization header
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip "Bearer " if present
        $token = preg_replace('/^Bearer\s+/', '', $token);

        // Decode token
        try {
            $decoded = JWT::decode($token, $this->jwt_key, array('HS512'));
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage(),
                'token' => $token,
                'decoded' => $this->jwt_key
            ], 401);
        }

        if (!isset($data['status'])) {
            return $this->response([
                'status' => false,
                'message' => 'Status is required'
            ], 400);
        }

        // Prepare data
        $save_data = [
            'status' => $data['status'],
            'sms' => isset($data['sms']) ? $data['sms'] : 'no',
            'email' => isset($data['email']) ? $data['email'] : 'no',
            'whatsapp' => isset($data['whatsapp']) ? $data['whatsapp'] : 'no',
            'ivr' => isset($data['ivr']) ? $data['ivr'] : 'no',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Insert or update
        $existing = $this->db->get_where('user_communication_preferences', [
            'user_id' => $user_id,
            'status' => $data['status']
        ]);
        if ($existing->num_rows() > 0) {
            $this->db->where('user_id', $user_id);
            $this->db->where('status', $data['status']);
            $this->db->update('user_communication_preferences', $save_data);
        } else {
            $save_data['user_id'] = $user_id;
            $save_data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('user_communication_preferences', $save_data);
        }

        $this->response([
            'status' => true,
            'message' => 'Settings saved successfully'
        ], 200);
    }

    // 2. Fetch Preferences
    public function getSettings_get()
    {
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        $token = preg_replace('/^Bearer\s+/', '', $token);

        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        $query = $this->db->get_where('user_communication_preferences', ['user_id' => $user_id]);
        if ($query->num_rows() > 0) {
            $this->response([
                'status' => true,
                'data' => $query->result_array()
            ], 200);
        } else {
            $this->response([
                'status' => true,
                'message' => 'No settings found for this user'
            ], 200);
        }
    }

    public function getCommunicationBalance_get()
    {
        $token = $this->input->get_request_header('Authorization');

        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is missing'
            ], 401);
        }

        // Step 1: Get user ID from token (adjust this part as per your auth logic)
        $token = preg_replace('/^Bearer\s+/', '', $token);

        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Step 2: Fetch communication balance
        $this->db->select('communication_balance');
        $this->db->from('users');
        $this->db->where('id', $user_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $this->response([
                'status' => true,
                'communication_balance' => $query->row()->communication_balance
            ], 200);
        } else {
            return $this->response([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }
    }

    public function transferWalletToCommunication_post()
    {
        // Get the token from header
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip Bearer
        $token = preg_replace('/^Bearer\s+/', '', $token);

        // Decode token
        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Get transfer amount from request
        $input_json = $this->input->raw_input_stream;
        $data = json_decode($input_json, true);
        $amount = isset($data['amount']) ? floatval($data['amount']) : 0;

        if ($amount <= 0) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid transfer amount'
            ], 400);
        }

        // Fetch user
        $user = $this->db->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user) {
            return $this->response([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        $wallet_balance = floatval($user->wallet_balance);
        $wallet_limit = floatval($user->wallet_limit);
        $communication_balance = floatval($user->communication_balance);

        // Calculate if transfer is allowed
        $min_allowed_balance = 0;  // e.g. -3000
        $new_wallet_balance = $wallet_balance - $amount;

        if ($new_wallet_balance < $min_allowed_balance) {
            return $this->response([
                'status' => false,
                'message' => 'Insufficient Wallet Balance'
            ], 403);
        }

        // Perform update in transaction
        $this->db->trans_start();

        // Update balances
        $new_communication_balance = $communication_balance + $amount;
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users', [
            'wallet_balance' => $new_wallet_balance,
            'communication_balance' => $new_communication_balance
        ]);

        // Log the transaction
        $this->db->insert('tbl_wallet_to_communication_transactions', [
            'user_id' => $user_id,
            'amount' => $amount,
            'previous_wallet_balance' => $wallet_balance,
            'new_wallet_balance' => $new_wallet_balance,
            'previous_communication_balance' => $communication_balance,
            'new_communication_balance' => $new_communication_balance,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return $this->response([
                'status' => false,
                'message' => 'Transfer failed. Please try again.'
            ], 500);
        }

        return $this->response([
            'status' => true,
            'message' => 'Transfer successful',
            'data' => [
                'wallet_balance' => $new_wallet_balance,
                'communication_balance' => $new_communication_balance
            ]
        ], 200);
    }

/* 
    public function update_communication_pricing_post()
    {
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip Bearer
        $token = preg_replace('/^Bearer\s+/', '', $token);

        // Decode token
        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Check if user is admin
        $user = $this->db->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user || $user->is_admin != 1) {
            return $this->response(['status' => false, 'message' => 'Unauthorized access'], 403);
        }

        // Get pricing data from request body
        $body = json_decode($this->input->raw_input_stream);
        // Get pricing data from request body
        $seller_id = $body->seller_id;
        $sms = $body->sms;
        $whatsapp = $body->whatsapp;
        $email = $body->email;
        $ivr = $body->ivr;

        // Check if entry already exists for this user
        $existing = $this->db->get_where('tbl_communication_pricings', ['user_id' => $seller_id])->row();

        $data = [
            'sms' => $sms,
            'whatsapp' => $whatsapp,
            'email' => $email,
            'ivr' => $ivr,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            $this->db->where('user_id', $seller_id);
            $this->db->update('tbl_communication_pricings', $data);
        } else {
            $data['user_id'] = $seller_id;
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('tbl_communication_pricings', $data);
        }

        return $this->response([
            'status' => true,
            'message' => 'Pricing updated successfully',
        ], 200);
    }
 */

    public function update_communication_pricing_post()
    {
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip Bearer
        $token = preg_replace('/^Bearer\s+/', '', $token);

        // Decode token
        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Check if user is admin
        $user = $this->db->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user || $user->is_admin != 1) {
            return $this->response(['status' => false, 'message' => 'Unauthorized access'], 403);
        }

        // Get pricing data from request body
        $body = json_decode($this->input->raw_input_stream);

        // Extract data from request
        $seller_id = $body->seller_id;
        $sms = $body->sms;
        $whatsapp = $body->whatsapp;
        $email = $body->email;
        $ivr = $body->ivr;
        $service_provider = $body->service_provider;

        // Check if entry already exists for this user and service provider
        $existing = $this->db->get_where('tbl_communication_pricings', [
            'user_id' => $seller_id,
            'service_provider' => $service_provider
        ])->row();

        $data = [
            'sms' => $sms,
            'whatsapp' => $whatsapp,
            'email' => $email,
            'ivr' => $ivr,
            'updated_at' => date('Y-m-d H:i:s'),
            'service_provider' => $service_provider
        ];

        if ($existing) {
            $this->db->where([
                'user_id' => $seller_id,
                'service_provider' => $service_provider
            ]);
            $this->db->update('tbl_communication_pricings', $data);
        } else {
            $data['user_id'] = $seller_id;
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('tbl_communication_pricings', $data);
        }

        return $this->response([
            'status' => true,
            'message' => 'Pricing updated successfully',
        ], 200);
    }
/* 
    public function get_seller_pricings_post()
    {
        // Step 1: Get and validate the token
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip Bearer if present
        $token = preg_replace('/^Bearer\s+/', '', $token);

        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Step 2: Check if the user is admin
        $user = $this->db->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user || $user->is_admin != 1) {
            return $this->response([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Step 3: Get raw input data
        $rawInput = json_decode($this->input->raw_input_stream, true);
        $seller_id = $rawInput['seller_id'] ?? null;

        if (!$seller_id) {
            return $this->response([
                'status' => false,
                'message' => 'sellerid required in body'
            ], 400);
        }

        $query = $this->db->get_where('tbl_communication_pricings', ['user_id' => $seller_id]);
            if ($query->num_rows() > 0) {
                $this->response([
                    'status' => true,
                    'data' => $query->row_array()
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No pricing found for this user'
                ], 404);
            }
    }
 */

    public function get_seller_pricings_post()
    {
        // Step 1: Get and validate the token
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip Bearer if present
        $token = preg_replace('/^Bearer\s+/', '', $token);

        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Step 2: Check if the user is admin
        $user = $this->db->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user || $user->is_admin != 1) {
            return $this->response([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Step 3: Get raw input data
        $rawInput = json_decode($this->input->raw_input_stream, true);
        $seller_id = $rawInput['seller_id'] ?? null;
         $service_provider = $rawInput['service_provider'] ?? null;

        if (!$seller_id) {
            return $this->response([
                'status' => false,
                'message' => 'sellerid required in body'
            ], 400);
        }

        $query = $this->db->get_where('tbl_communication_pricings', ['user_id' => $seller_id, 'service_provider' => $service_provider]);
            if ($query->num_rows() > 0) {
                $this->response([
                    'status' => true,
                    'data' => $query->row_array()
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No pricing found for this user'
                ], 404);
            }
    }

    public function get_service_provider_post()
    {
        // Step 1: Get token from headers
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required'
            ], 400);
        }

        $token = preg_replace('/^Bearer\s+/', '', $token);

        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Step 2: Check if user is admin
        $admin = $this->db->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$admin || $admin->is_admin != 1) {
            return $this->response([
                'status' => false,
                'message' => 'Access denied. Only admin can perform this action.'
            ], 403);
        }

        // Step 3: Get seller ID from POST body
        $rawInput = json_decode($this->input->raw_input_stream, true);
        $seller_id = $rawInput['seller_id'] ?? null;
        if (!$seller_id) {
            return $this->response([
                'status' => false,
                'message' => 'sellerid is required'
            ], 400);
        }

        // Step 4: Fetch service provider from communication preferences
        $this->db->select('communication_provider');
        $this->db->from('tbl_users');
        $this->db->where('id', $seller_id);
        $row = $this->db->get()->row();

        if ($row) {
            return $this->response([
                'status' => true,
                'service_provider' => $row->communication_provider
            ], 200);
        } else {
            return $this->response([
                'status' => false,
                'message' => 'No record found for this seller ID'
            ], 404);
        }
    }

    public function update_service_provider_post()
    {
        // Step 1: Get and validate the token
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip Bearer if present
        $token = preg_replace('/^Bearer\s+/', '', $token);

        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Step 2: Check if the user is admin
        $user = $this->db->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user || $user->is_admin != 1) {
            return $this->response([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Step 3: Get raw input data
        $rawInput = json_decode($this->input->raw_input_stream, true);
        $seller_id = $rawInput['seller_id'] ?? null;
        $service_provider = $rawInput['serviceprovider'] ?? null;

        if (!$seller_id || !$service_provider) {
            return $this->response([
                'status' => false,
                'message' => 'sellerid and serviceprovider are required in body'
            ], 400);
        }

        // Step 4: Update service_provider for matching records
        $this->db->where('id', $seller_id);
        $this->db->update('tbl_users', [
            'communication_provider' => $service_provider
        ]);

        return $this->response([
            'status' => true,
            'message' => 'Service provider updated successfully'
        ], 200);
    }

    public function get_seller_pricings_service_get()
    {
        // Step 1: Get and validate the token
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip Bearer if present
        $token = preg_replace('/^Bearer\s+/', '', $token);

        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }


        $query = $this->db->get_where('tbl_communication_bundeled_price');
        $query2 = $this->db->get_where('tbl_communication_individual_price');
        $output = [
            'bundled' => $query->result_array(),
            'individual' => $query2->result_array()
        ];

        if ($query->num_rows() > 0 && $query2->num_rows() > 0) {
            $this->response([
                'status' => true,
                'data' => $output
            ], 200);
        } else {
            $this->response([
                'status' => true,
                'message' => 'No pricing found for this user'
            ], 200);
        }

    }

    public function set_seller_pricings_service_post()
    {
        // Step 1: Get and validate the token
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip Bearer if present
        $token = preg_replace('/^Bearer\s+/', '', $token);

        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Step 2: Get the input payload
        $input = json_decode(trim(file_get_contents('php://input')), true);

        if (empty($input)) {
            return $this->response([
                'status' => false,
                'message' => 'No input data provided'
            ], 400);
        }

        // Step 3: Update bundled pricing
        if (!empty($input['bundled']) && is_array($input['bundled'])) {
            foreach ($input['bundled'] as $item) {
                if (!isset($item['id'])) continue;

                $update_data = [];
                if (isset($item['sms'])) $update_data['sms'] = $item['sms'];
                if (isset($item['whatsapp'])) $update_data['whatsapp'] = $item['whatsapp'];
                if (isset($item['ivr'])) $update_data['ivr'] = $item['ivr'];
                if (isset($item['email'])) $update_data['email'] = $item['email'];

                $this->db->where('id', $item['id']);
                $this->db->update('tbl_communication_bundeled_price', $update_data);
            }
        }

        // Step 4: Update individual pricing
        if (!empty($input['individual']) && is_array($input['individual'])) {
            foreach ($input['individual'] as $item) {
                if (!isset($item['id'])) continue;

                $update_data = [];
                if (isset($item['status'])) $update_data['status'] = $item['status'];
                if (isset($item['sms'])) $update_data['sms'] = $item['sms'];
                if (isset($item['whatsapp'])) $update_data['whatsapp'] = $item['whatsapp'];
                if (isset($item['ivr'])) $update_data['ivr'] = $item['ivr'];
                if (isset($item['email'])) $update_data['email'] = $item['email'];

                $this->db->where('id', $item['id']);
                $this->db->update('tbl_communication_individual_price', $update_data);
            }
        }

        return $this->response([
            'status' => true,
            'message' => 'Pricing updated successfully'
        ], 200);
    }


    public function get_buying_pricings_post()
    {
        // Step 1: Get and validate the token
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip Bearer if present
        $token = preg_replace('/^Bearer\s+/', '', $token);

        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Step 2: Check if the user is admin
        $user = $this->db->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user || $user->is_admin != 1) {
            return $this->response([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Step 3: Get raw input data
        $rawInput = json_decode($this->input->raw_input_stream, true);
        $service_provider = $rawInput['service_provider'] ?? null;

        if (!$service_provider) {
            return $this->response([
                'status' => false,
                'message' => 'service_provider required in body'
            ], 400);
        }

        $query = $this->db->get_where('tbl_communication_buyingprice', ['service_provider' => $service_provider]);
            if ($query->num_rows() > 0) {
                $this->response([
                    'status' => true,
                    'data' => $query->row_array()
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No pricing found for this service provider'
                ], 404);
            }

        
    }

    public function savebrandname_post()
    {
        $input_json = $this->input->raw_input_stream;
        $data = json_decode($input_json, true);

        // Get token from Authorization header
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip "Bearer " if present
        $token = preg_replace('/^Bearer\s+/', '', $token);

        // Decode token
        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage(),
                'token' => $token,
                'decoded' => $this->jwt_key
            ], 401);
        }

        // Prepare brand_name (set to NULL if not provided or empty)
        $brand_name = isset($data['brand_name']) && !empty(trim($data['brand_name'])) ? trim($data['brand_name']) : null;

        // Check if user exists
        $user = $this->db->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user) {
            return $this->response([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Update brand_name
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users', ['brand_name' => $brand_name]);

        return $this->response([
            'status' => true,
            'message' => 'Brand name updated successfully',
            'brand_name' => $brand_name
        ], 200);
    }

    public function getbrandname_post()
    {
        // Get token from Authorization header
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip "Bearer " if present
        $token = preg_replace('/^Bearer\s+/', '', $token);

        // Decode token
        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Fetch user data
        $user = $this->db->get_where('tbl_users', ['id' => $user_id])->row();

        if (!$user) {
            return $this->response([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        return $this->response([
            'status' => true,
            'brand_name' => $user->brand_name ?? null
        ], 200);
    }

    public function set_communication_plan_post()
    {
        // Step 1: Get and validate token
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        $token = preg_replace('/^Bearer\s+/', '', $token);
        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Step 2: Parse input JSON
        $input_json = $this->input->raw_input_stream;
        $data = json_decode($input_json, true);

        if (!isset($data['communication_plan'])) {
            return $this->response([
                'status' => false,
                'message' => 'communication_plan is required'
            ], 400);
        }

        $plan = strtolower(trim($data['communication_plan']));
        $update_data = ['communication_plan' => $plan];

        if ($plan === 'individual') {
            // Set all specifics to NULL
            $update_data['communication_specific1'] = null;
            $update_data['communication_specific2'] = null;
            $update_data['communication_specific3'] = null;
            $update_data['communication_specific4'] = null;
        } elseif ($plan === 'bundled') {
            // Set specifics (null if not provided)
            $update_data['communication_specific1'] = isset($data['communication_specific1']) ? $data['communication_specific1'] : null;
            $update_data['communication_specific2'] = isset($data['communication_specific2']) ? $data['communication_specific2'] : null;
            $update_data['communication_specific3'] = isset($data['communication_specific3']) ? $data['communication_specific3'] : null;
            $update_data['communication_specific4'] = isset($data['communication_specific4']) ? $data['communication_specific4'] : null;
        } else {
            return $this->response([
                'status' => false,
                'message' => 'Invalid communication_plan. Must be "individual" or "bundled".'
            ], 400);
        }

        // Step 3: Update database
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users', $update_data);

        return $this->response([
            'status' => true,
            'message' => 'Communication plan updated successfully',
            'updated_fields' => $update_data
        ], 200);
    }

    public function get_communication_plan_post()
    {
        // Step 1: Get and validate token
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        $token = preg_replace('/^Bearer\s+/', '', $token);
        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Step 2: Fetch user communication settings
        $user = $this->db->select('communication_plan, communication_specific1, communication_specific2, communication_specific3, communication_specific4')
                        ->get_where('tbl_users', ['id' => $user_id])
                        ->row();

        if (!$user) {
            return $this->response([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Step 3: Return communication plan and specifics
        return $this->response([
            'status' => true,
            'communication_plan' => $user->communication_plan,
            'communication_specific1' => $user->communication_specific1,
            'communication_specific2' => $user->communication_specific2,
            'communication_specific3' => $user->communication_specific3,
            'communication_specific4' => $user->communication_specific4
        ], 200);
    }

    public function get_bundel_price_post()
    {
        // Step 1: Get and validate token
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        $token = preg_replace('/^Bearer\s+/', '', $token);
        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Step 2: Check if user exists
        $user = $this->db->select('id')->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user) {
            return $this->response([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Step 3: Fetch bundled price from the price table
        $bundled = $this->db->get('tbl_communication_bundeled_price')->row();
        if (!$bundled) {
            return $this->response([
                'status' => false,
                'message' => 'Bundled price not configured yet'
            ], 404);
        }

        // Step 4: Return the bundled price
        return $this->response([
            'status' => true,
            'message' => 'Bundled price retrieved successfully',
            'data' => [
                'sms' => $bundled->sms,
                'whatsapp' => $bundled->whatsapp,
                'ivr' => $bundled->ivr,
                'email' => $bundled->email
            ]
        ], 200);
    }

    public function get_individual_price_post()
    {
        // Step 1: Get and validate token
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        $token = preg_replace('/^Bearer\s+/', '', $token);
        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Step 2: Check if user exists
        $user = $this->db->select('id')->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user) {
            return $this->response([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Step 3: Fetch individual prices for all statuses
        $individualPrices = $this->db
            ->select('status, sms, whatsapp, ivr, email')
            ->from('tbl_communication_individual_price')
            ->get()
            ->result();

        if (!$individualPrices) {
            return $this->response([
                'status' => false,
                'message' => 'No individual pricing data found'
            ], 404);
        }

        // Step 4: Return the pricing list
        return $this->response([
            'status' => true,
            'message' => 'Individual prices fetched successfully',
            'data' => $individualPrices
        ], 200);
    }

    public function get_seller_individual_pricings_post()
    {
        // Step 1: Get and validate the token
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip Bearer if present
        $token = preg_replace('/^Bearer\s+/', '', $token);

        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Step 3: Get raw input data
        $rawInput = json_decode($this->input->raw_input_stream, true);
        $seller_id = $rawInput['seller_id'] ?? null;

        // Step 2: Check if the user is admin
        $user = $this->db->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user || $user->is_admin != 1) {
            $seller_id = $user_id;
        }

        

        if (!$seller_id) {
            return $this->response([
                'status' => false,
                'message' => 'sellerid required in body'
            ], 400);
        }

        $query = $this->db->get_where('tbl_user_individual_price', ['user_id' => $seller_id]);
            if ($query->num_rows() > 0) {
                $this->response([
                    'status' => true,
                    'data' => $query->result()
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No pricing found for this user'
                ], 404);
            }
    }
    
    /* 
    public function get_seller_individual_pricings_post()
    {
        // Step 1: Get and validate the token
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip Bearer if present
        $token = preg_replace('/^Bearer\s+/', '', $token);

        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Step 2: Check if the user is admin
        $user = $this->db->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user || $user->is_admin != 1) {
            return $this->response([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Step 3: Get raw input data
        $rawInput = json_decode($this->input->raw_input_stream, true);
        $seller_id = $rawInput['seller_id'] ?? null;

        if (!$seller_id) {
            return $this->response([
                'status' => false,
                'message' => 'sellerid required in body'
            ], 400);
        }

        $query = $this->db->get_where('tbl_user_individual_price', ['user_id' => $seller_id]);
            if ($query->num_rows() > 0) {
                $this->response([
                    'status' => true,
                    'data' => $query->result()
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No pricing found for this user'
                ], 404);
            }
    }
    */
    
    public function get_seller_bundled_pricings_post()
    {
        // Step 1: Get and validate the token
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip Bearer if present
        $token = preg_replace('/^Bearer\s+/', '', $token);

        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }
        
        // Step 3: Get raw input data
        $rawInput = json_decode($this->input->raw_input_stream, true);
        $seller_id = $rawInput['seller_id'] ?? null;

        // Step 2: Check if the user is admin
        $user = $this->db->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user || $user->is_admin != 1) {
            $seller_id = $user_id;
        }


        if (!$seller_id) {
            return $this->response([
                'status' => false,
                'message' => 'sellerid required in body'
            ], 400);
        }

        $query = $this->db->get_where('tbl_user_bundeled_price', ['user_id' => $seller_id]);
            if ($query->num_rows() > 0) {
                $this->response([
                    'status' => true,
                    'data' => $query->row_array()
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No pricing found for this user'
                ], 404);
            }
    }
    
    /* 
    public function get_seller_bundled_pricings_post()
    {
        // Step 1: Get and validate the token
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip Bearer if present
        $token = preg_replace('/^Bearer\s+/', '', $token);

        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Step 2: Check if the user is admin
        $user = $this->db->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user || $user->is_admin != 1) {
            return $this->response([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Step 3: Get raw input data
        $rawInput = json_decode($this->input->raw_input_stream, true);
        $seller_id = $rawInput['seller_id'] ?? null;

        if (!$seller_id) {
            return $this->response([
                'status' => false,
                'message' => 'sellerid required in body'
            ], 400);
        }

        $query = $this->db->get_where('tbl_user_bundeled_price', ['user_id' => $seller_id]);
            if ($query->num_rows() > 0) {
                $this->response([
                    'status' => true,
                    'data' => $query->row_array()
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No pricing found for this user'
                ], 404);
            }
    } 
    */


    public function update_seller_individual_pricing_post()
    {
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip Bearer
        $token = preg_replace('/^Bearer\s+/', '', $token);

        // Decode token
        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Check if user is admin
        $user = $this->db->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user || $user->is_admin != 1) {
            return $this->response(['status' => false, 'message' => 'Unauthorized access'], 403);
        }

        // Get array of pricing data from request body
        $body = json_decode($this->input->raw_input_stream);

        if (empty($body->seller_id) || empty($body->pricings) || !is_array($body->pricings)) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid request: seller_id and pricings array are required'
            ], 400);
        }

        $seller_id = $body->seller_id;
        $pricings  = $body->pricings; // array of objects

        foreach ($pricings as $pricing) {
            $status    = $pricing->status ?? '';
            $sms       = $pricing->sms ?? 0;
            $whatsapp  = $pricing->whatsapp ?? 0;
            $email     = $pricing->email ?? 0;
            $ivr       = $pricing->ivr ?? 0;

            // Check if entry already exists for this user + status
            $existing = $this->db->get_where('tbl_user_individual_price', [
                'user_id' => $seller_id,
                'status'  => $status
            ])->row();

            $data = [
                'status'     => $status,
                'sms'        => $sms,
                'whatsapp'   => $whatsapp,
                'email'      => $email,
                'ivr'        => $ivr,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                $this->db->where([
                    'user_id' => $seller_id,
                    'status'  => $status
                ])->update('tbl_user_individual_price', $data);
            } else {
                $data['user_id']    = $seller_id;
                $data['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('tbl_user_individual_price', $data);
            }
        }

        return $this->response([
            'status'  => true,
            'message' => 'Pricing updated successfully for all statuses',
        ], 200);
    }

    public function update_seller_bundled_pricing_post()
    {
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip Bearer
        $token = preg_replace('/^Bearer\s+/', '', $token);

        // Decode token
        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Check if user is admin
        $user = $this->db->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user || $user->is_admin != 1) {
            return $this->response(['status' => false, 'message' => 'Unauthorized access'], 403);
        }

        // Get pricing data from request body
        $body = json_decode($this->input->raw_input_stream);

        if (empty($body->seller_id)) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid request: seller_id is required'
            ], 400);
        }

        $seller_id = $body->seller_id;
        $sms       = $body->sms ?? 0;
        $whatsapp  = $body->whatsapp ?? 0;
        $email     = $body->email ?? 0;
        $ivr       = $body->ivr ?? 0;

        // Check if bundled entry already exists for this user
        $existing = $this->db->get_where('tbl_user_bundeled_price', [           //>Message: Table 'daakit_shipping.tbl_user_bundled_price' doesn't exist
            'user_id' => $seller_id
        ])->row();

        $data = [
            'sms'        => $sms,
            'whatsapp'   => $whatsapp,
            'email'      => $email,
            'ivr'        => $ivr,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            // Update existing bundled pricing
            $this->db->where('user_id', $seller_id)
                    ->update('tbl_user_bundeled_price', $data);
        } else {
            // Insert new bundled pricing
            $data['user_id']    = $seller_id;
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('tbl_user_bundeled_price', $data);
        }

        return $this->response([
            'status'  => true,
            'message' => 'Bundled pricing updated successfully',
        ], 200);
    }

    public function remove_seller_individual_pricing_post()
    {
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip Bearer
        $token = preg_replace('/^Bearer\s+/', '', $token);

        // Decode token
        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Check if user is admin
        $user = $this->db->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user || $user->is_admin != 1) {
            return $this->response(['status' => false, 'message' => 'Unauthorized access'], 403);
        }

        // Get request body
        $body = json_decode($this->input->raw_input_stream);

        if (empty($body->seller_id)) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid request: seller_id and status are required'
            ], 400);
        }

        $seller_id = $body->seller_id;

        // Check if record exists
        $existing = $this->db->get_where('tbl_user_individual_price', [
            'user_id' => $seller_id
        ])->row();

        if (!$existing) {
            return $this->response([
                'status' => false,
                'message' => 'No pricing found for this user and status'
            ], 404);
        }

        // Delete record
        $this->db->where([
            'user_id' => $seller_id
        ])->delete('tbl_user_individual_price');

        return $this->response([
            'status' => true,
            'message' => 'Pricing for status removed successfully'
        ], 200);
    }
    /* 
    public function remove_seller_individual_pricing_post()
    {
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip Bearer
        $token = preg_replace('/^Bearer\s+/', '', $token);

        // Decode token
        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Check if user is admin
        $user = $this->db->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user || $user->is_admin != 1) {
            return $this->response(['status' => false, 'message' => 'Unauthorized access'], 403);
        }

        // Get request body
        $body = json_decode($this->input->raw_input_stream);

        if (empty($body->seller_id) || empty($body->status)) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid request: seller_id and status are required'
            ], 400);
        }

        $seller_id = $body->seller_id;
        $status    = $body->status;

        // Check if record exists
        $existing = $this->db->get_where('tbl_user_individual_price', [
            'user_id' => $seller_id,
            'status'  => $status
        ])->row();

        if (!$existing) {
            return $this->response([
                'status' => false,
                'message' => 'No pricing found for this user and status'
            ], 404);
        }

        // Delete record
        $this->db->where([
            'user_id' => $seller_id,
            'status'  => $status
        ])->delete('tbl_user_individual_price');

        return $this->response([
            'status' => true,
            'message' => 'Pricing for status removed successfully'
        ], 200);
    } */

    public function remove_seller_bundled_pricing_post()
    {
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip Bearer
        $token = preg_replace('/^Bearer\s+/', '', $token);

        // Decode token
        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Check if user is admin
        $user = $this->db->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user || $user->is_admin != 1) {
            return $this->response(['status' => false, 'message' => 'Unauthorized access'], 403);
        }

        // Get request body
        $body = json_decode($this->input->raw_input_stream);

        if (empty($body->seller_id)) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid request: seller_id is required'
            ], 400);
        }

        $seller_id = $body->seller_id;

        // Delete bundled pricing for this seller
        $this->db->where('user_id', $seller_id);
        $deleted = $this->db->delete('tbl_user_bundeled_price');

        if ($deleted) {
            return $this->response([
                'status'  => true,
                'message' => 'Bundled pricing removed successfully',
            ], 200);
        } else {
            return $this->response([
                'status'  => false,
                'message' => 'No bundled pricing found for this seller or deletion failed',
            ], 404);
        }
    }

    public function get_all_users_post()
    {
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        // Strip Bearer
        $token = preg_replace('/^Bearer\s+/', '', $token);

        // Decode token
        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Check if user is admin
        $user = $this->db->get_where('tbl_users', ['id' => $user_id])->row();
        if (!$user || $user->is_admin != 1) {
            return $this->response(['status' => false, 'message' => 'Unauthorized access'], 403);
        }

        // Fetch all users
        $users = $this->db->select('id, fname, lname, email')                                                 //Name is not defiend
                        ->from('tbl_users')
                        ->where('is_admin','0')
                        ->get()
                        ->result();

        return $this->response([
            'status' => true,
            'message' => 'Users fetched successfully',
            'data' => $users
        ], 200);
    }


    public function get_communication_plan_seller_post()
    {
        // Step 1: Get and validate token
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        $token = preg_replace('/^Bearer\s+/', '', $token);
        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        $body = json_decode($this->input->raw_input_stream);

        if (empty($body->seller_id)) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid request: seller_id is required'
            ], 400);
        }

        $seller_id = $body->seller_id;

        // Step 2: Fetch user communication settings
        $user = $this->db->select('communication_plan, communication_specific1, communication_specific2, communication_specific3, communication_specific4')
                        ->get_where('tbl_users', ['id' => $seller_id])
                        ->row();

        if (!$user) {
            return $this->response([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Step 3: Return communication plan and specifics
        return $this->response([
            'status' => true,
            'communication_plan' => $user->communication_plan,
            'communication_specific1' => $user->communication_specific1,
            'communication_specific2' => $user->communication_specific2,
            'communication_specific3' => $user->communication_specific3,
            'communication_specific4' => $user->communication_specific4
        ], 200);
    }


    public function getSettings_seller_post()
    {
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Authorization token is required in headers'
            ], 400);
        }

        $token = preg_replace('/^Bearer\s+/', '', $token);

        try {
            $decoded = JWT::decode($token, $this->jwt_key, ['HS512']);
            $user_id = $decoded->data->user_id;
        } catch (Exception $e) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        $body = json_decode($this->input->raw_input_stream);

        if (empty($body->seller_id)) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid request: seller_id is required'
            ], 400);
        }

        $seller_id = $body->seller_id;

        $query = $this->db->get_where('user_communication_preferences', ['user_id' => $seller_id]);
        if ($query->num_rows() > 0) {
            $this->response([
                'status' => true,
                'data' => $query->result_array()
            ], 200);
        } else {
            $this->response([
                'status' => true,
                'message' => 'No settings found for this user'
            ], 200);
        }
    }


}