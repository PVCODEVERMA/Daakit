<?php

defined('BASEPATH') or exit('No direct script access allowed');

class DaakitGo extends MY_lib
{
    private $api_key;
    private $secret_key;
    private $api_url;

    public function __construct($config = false)
    {
        parent::__construct();
        $this->CI = &get_instance();
        $this->CI->load->database();
        $this->db = $this->CI->db;

       
        $this->api_key = 'fb2df6f8986304efc2c3e02babb7278d';
        $this->secret_key = '76472f4cb5cb9f5d57d73e642a93f844660b1a6ed03dd3eeda8a2a05600058f9';

        $this->api_url = 'https://go-admin.daakit.com/api/merchant/createOrder';
    }

    public function createOrder($order = array())
    {
        if (empty($order)) {
            $this->error = 'Invalid Order';
            return false;
        }

        
        $pickup_address = [
            "contact_name" => $order['pickup']['contact_name'] ?? $order['pickup']['name'] ?? '',
            "phone" => $order['pickup']['phone'] ?? '',
            "address_line" => trim($order['pickup']['address_1'] . ' ' . $order['pickup']['address_2']),
            "pincode" => $order['pickup']['zip'],
            "city" => $order['pickup']['city'],
            "state" => $order['pickup']['state']
        ];

        $delivery_address = [
            "contact_name" => $order['customer']['name'],
            "phone" => $order['customer']['phone'],
            "address_line" => trim($order['customer']['address'] . ' ' . $order['customer']['address_2']),
            "pincode" => $order['customer']['zip'],
            "city" => $order['customer']['city'],
            "state" => $order['customer']['state']
        ];

        $return_address = [];
        if (!empty($order['rto'])) {
            $return_address = [
                "contact_name" => $order['rto']['contact_name'],
                "phone" => $order['rto']['phone'],
                "address_line" => trim($order['rto']['address_1'] . ' ' . $order['rto']['address_2']),
                "pincode" => $order['rto']['zip'],
                "city" => $order['rto']['city'],
                "state" => $order['rto']['state']
            ];
        } else {
            $return_address = $pickup_address;
        }

        $items = [];
        if (!empty($order['products'])) {
            foreach ($order['products'] as $product) {
                $items[] = [
                    "sku" => $product['sku'],
                    "product_name" => $product['name'],
                    "quantity" => $product['qty'],
                    "unit_price" => $product['price'],
                    "weight" => $product['weight']
                ];
            }
        }

       $courier_id = $order['courier']['id'] ?? null;
       $courier_code = 'SDD-500gm';

    if ($courier_id) {
       $courier = $this->db->get_where('tbl_courier', ['id' => $courier_id])->row();
       if ($courier && isset($courier->code)) {
        $courier_code = trim($courier->code);
    }
}

       $ord = $order['order'];
       $ord['courier_code'] = $courier_code;
        $order_details = [
            "order_type" => $ord['payment_method'],
            "orderno" => $ord['seller_order_id'],
            "total_amount" => $ord['total'],
            "collectable_amount" => ($ord['payment_method'] == 'cod') ? $ord['total'] : 0,
            "total_weight" => strval($ord['weight']),
            "number_of_boxes" => 1,
            "package_length" => $ord['length'],
            "breadth" => $ord['breadth'],
            "height" => $ord['height'],
            "items" => $items,
            "order_specific" => $ord['courier_code']
        ];

        $payload = [
            "pickup_address" => $pickup_address,
            "delivery_address" => $delivery_address,
            "return_address" => $return_address,
            "order_details" => $order_details
        ];

        $json_payload = json_encode($payload);

       
        $signature = hash_hmac('sha256', $json_payload, $this->secret_key);

        
        $ch = curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-merchant-key: ' . $this->api_key,
            'x-signature: ' . $signature
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);

        $response = curl_exec($ch);
        $error_msg = curl_error($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);

       
        do_action('log.create', 'shipment', [
            'action' => 'daakitgo_request_response',
            'ref_id' => $ord['shipment_id'],
            'user_id' => $ord['user_id'],
            'data' => [
                'request' => $payload,
                'response' => $responseData,
                'curl_error' => $error_msg
            ]
        ]);

        if ($error_msg) {
            $this->error = $error_msg;
            return false;
        }

        if (isset($responseData['success']) && $responseData['success'] && !empty($responseData['awb_number'])) {
            return [
                $ord['shipment_id'] => [
                    'status' => 'success',
                    'awb' => $responseData['awb_number'],
                    'shipment_info_1' => $responseData['message'] ?? '',
                    'shipment_weight' => $ord['weight']
                ]
            ];
        } else {
            $this->error = $responseData['message'] ?? 'DaakitGo: Unknown API error';
            return false;
        }
    }

public function cancelAWB($awb = false)
{
    if (!$awb) {
        $this->error = 'AWB number is missing';
        return false;
    }

    $url = 'https://go-admin.daakit.com/api/merchant/cancelorder';

    $payload = ['awb_number' => $awb];
    $raw = json_encode($payload);

    $signature = hash_hmac('sha256', $raw, $this->secret_key);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "Content-Type: application/json",
            "x-merchant-key: " . $this->api_key,
            "x-signature: " . $signature
        ),
        CURLOPT_POSTFIELDS => $raw
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $responseData = json_decode($response, true);

    // do_action('log.create', 'shipment', [
    //     'action' => 'daakitgo_cancel_request_response',
    //     'ref_id' => $awb,
    //     'data' => [
    //         'url' => $url,
    //         'request_raw' => $raw,
    //         'signature' => $signature,
    //         'response' => $responseData,
    //         'curl_error' => $err
    //     ]
    // ]);

    if ($err) {
        $this->error = $err;
        return false;
    }

    if (isset($responseData['success']) && $responseData['success']) {
        return true;
    }

    $this->error = $responseData['message'] ?? 'DaakitGo: Unable to cancel AWB';
    return false;
}

public function pushNDRAction($ndr_data)
{
    if (empty($ndr_data) || empty($ndr_data['awb_number'])) {
        $this->error = 'Invalid NDR payload';
        return false;
    }

    $actionMap = [
        're-attempt'      => 'reattempt',
        'change address' => 'change_address',
        'change phone'   => 'change_phone',
        'rto'            => 'rto'
    ];

    if (!isset($actionMap[$ndr_data['action']])) {
        $this->error = 'Unsupported NDR action';
        return false;
    }

    $payload = [
        'action' => $actionMap[$ndr_data['action']],
        'remarks' => $ndr_data['remarks'] ?? '',
        'customer_name' => $ndr_data['change_name'] ?? '',
        'customer_details_address' => trim(
            ($ndr_data['change_address_1'] ?? '') . ' ' .
            ($ndr_data['change_address_2'] ?? '')
        ),
        'customer_phone' => $ndr_data['change_phone'] ?? ''
    ];

    $json = json_encode($payload);


    $signature = hash_hmac('sha256', $json, $this->secret_key);

    $url = 'https://go-admin.daakit.com/api/merchant/pushNDRaction/' . $ndr_data['awb_number'];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $json,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'x-merchant-key: ' . $this->api_key,
            'x-signature: ' . $signature
        ],
        CURLOPT_TIMEOUT => 20
    ]);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);

    if ($err) {
        $this->error = $err;
        return false;
    }

    if (!empty($responseData['success'])) {
        return [
            'status' => 'success',
            'message' => $responseData['message'] ?? 'NDR pushed successfully'
        ];
    }

    $this->error = $responseData['message'] ?? 'DaakitGo NDR API failed';
    return false;
}

}
