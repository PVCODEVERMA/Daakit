<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Notification_lib extends MY_Lib
{
    public function __construct()
    {
        parent::__construct();
        $this->CI->load->database(); // this->CI already set by MY_Lib
    }

    public function sendNotification($shipmentId = null, $status = null, $orderIdInput = null)
{
    $response = [
        'shipment_id' => $shipmentId,
        'order_id' => $orderIdInput,
        'status' => $status,
        'attempted' => [],
        'skipped' => [],
        'errors' => []
    ];

    $shipment = null;
    $order = null;
    $user = null;
    $userId = null;
    $awb_number = null;
    $customerPhone = '';
    $orderId = null;
    $trackingUrl = '';
    $companyName = '';
    $sellerMobile = '';
    $shipmentIdToUse = null;

    if (!empty($shipmentId)) {
        $shipment = $this->CI->db->get_where('tbl_order_shipping', ['id' => $shipmentId])->row();
        if (!$shipment) {
            $response['errors'][] = 'Shipment not found';
            return $response;
        }
        $shipmentIdToUse = $shipment->id;

        $order = $this->CI->db->get_where('tbl_orders', ['id' => $shipment->order_id])->row();
        if ($order && !empty($order->shipping_phone)) {
            $customerPhone = '91' . $order->shipping_phone;
        }
        $orderId = $order->order_no;
        $awb_number = $shipment->awb_number;
        $orderIdInput = $order->id;
        $userId = $shipment->user_id;
        $trackingUrl = "https://app.daakit.com/awb/tracking/" . $shipment->awb_number;
    }

    if (empty($shipment) && !empty($orderIdInput)) {
        $order = $this->CI->db->get_where('tbl_orders', ['id' => $orderIdInput])->row();
        if (!$order) {
            $response['errors'][] = 'Order not found';
            return $response;
        }

        $orderId = $order->order_no;
        if (!empty($order->shipping_phone)) {
            $customerPhone = '91' . $order->shipping_phone;
        }

        $userId = $order->user_id;
        $trackingUrl = '';
    }

    $user = $this->CI->db->get_where('tbl_users', ['id' => $userId])->row();
    if (!$user) {
        $response['errors'][] = 'User not found';
        return $response;
    }

    $companyName = !empty($user->brand_name) ? $user->brand_name : $user->company_name;
    $sellerMobile = ($user->id == 29) ? '9810166781' : $user->phone;
    $plan = strtolower($user->communication_plan ?? '');

    // Skip if already sent
    $alreadySentQuery = $this->CI->db->where('status', $status);
    if (!empty($shipmentIdToUse)) {
        $alreadySentQuery->where('shipment_id', $shipmentIdToUse);
    } elseif (!empty($orderIdInput)) {
        $alreadySentQuery->where('order_id', $orderIdInput);
    }

    if ($alreadySentQuery->get('tbl_notification_responses')->num_rows() > 0) {
        $response['skipped'][] = 'Already sent for this status';
        return $response;
    }

    $smsprovider = $user->sms_provider;
    $whatsappprovider = $user->whatsapp_provider;
    $emailprovider = $user->email_provider;
    $ivrprovider = $user->ivr_provider;
    $channels = ['sms', 'whatsapp', 'email', 'ivr'];
    $specific = array_filter([
        $user->communication_specific1 ?? null,
        $user->communication_specific2 ?? null,
        $user->communication_specific3 ?? null,
        $user->communication_specific4 ?? null
    ]);

    // Preload all prices
    // $individualPrices = $this->CI->db->get_where('tbl_communication_individual_price', ['status' => strtolower($status)])->row();
    // $bundlePrices = $this->CI->db->get('tbl_communication_bundeled_price')->row();

     $individualPrices = $this->CI->db
        ->where('user_id', $userId)
        ->where('status', strtolower($status))
        ->get('tbl_user_individual_price')
        ->row();

    if (!$individualPrices) {
        $individualPrices = $this->CI->db
            ->get_where('tbl_communication_individual_price', ['status' => strtolower($status)])
            ->row();
    }

    // --- Preload Bundled Prices --- //
    $bundlePrices = $this->CI->db
        ->where('user_id', $userId)
        ->get('tbl_user_bundeled_price')
        ->row();

    if (!$bundlePrices) {
        $bundlePrices = $this->CI->db
            ->get('tbl_communication_bundeled_price')
            ->row();
    }

    foreach ($channels as $channel) {
        $send = false;
        $bundledPaidFlag = 0;

        if ($plan === 'individual') {
            $pref = $this->CI->db
                ->where('user_id', $userId)
                ->where('status', $status)
                ->get('tbl_user_communication_preferences')
                ->row();

            $bundledPaidExists = $this->CI->db->where('order_id', $orderIdInput)
                             ->where('channel_name', $channel)
                             ->where('bundled_paid', 1)
                             ->get('tbl_notification_responses')
                             ->num_rows() > 0;    

            if (!$pref) {
                $response['errors'][] = 'Communication preference not found';
                continue;
            }

            if (strtolower($pref->$channel ?? '') === 'yes') {
               if ($bundledPaidExists) {
                      // Bundled already covered this channel for this order
                  $send = true;
                  $bundledPaidFlag = 1;
                } else {
                     $priceBase = $individualPrices->$channel ?? 0;
                    $price = $priceBase + ($priceBase * 0.18);
                    $newBalance = $user->wallet_balance - $price;
                    if ($newBalance >= $user->wallet_limit) {
                        $send = true;
                    } else {
                        $response['skipped'][] = "$channel skipped due to insufficient wallet balance (Individual plan)";
                    }
                }
            }
        } elseif ($plan === 'bundled') {
            if (in_array($channel, $specific)) {
                $statusExists = !empty($individualPrices);
                if ($statusExists) {
                    $priceBase = $bundlePrices->$channel ?? 0;
                    $price = $priceBase + ($priceBase * 0.18);
                    $currentStatus = strtolower($status);
                    $orderSource = strtolower($order->order_source ?? '');
                    $initialStatuses = [];

                    if (in_array($channel, ['whatsapp', 'sms', 'email'])) {
                        if ($channel === 'whatsapp') {
                            $initialStatuses = ['new', 'confirmation acknowledgement', 'pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception'];
                        } else {
                            $initialStatuses = ['pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception'];
                        }
                    }

                    $captured = $this->isAnyStatusCaptured($orderIdInput, $channel, $initialStatuses);

                    if (in_array($currentStatus, $initialStatuses)) {
                        $newBalance = $user->wallet_balance - $price;
                        if (!$captured && $newBalance >= $user->wallet_limit) {
                            $send = true;
                        } elseif (!$captured) {
                            $response['skipped'][] = "$channel skipped due to insufficient balance";
                        } else {
                            $send = true;
                        }
                    } else {
                        if ($captured) {
                            $send = true;
                            $bundledPaidFlag = 1;
                        } else {
                            $response['skipped'][] = "$channel skipped because none of initial statuses (" . implode(', ', $initialStatuses) . ") were captured";
                        }
                    }
                } else {
                    $response['skipped'][] = "$channel skipped: status not allowed in bundled";
                }
            }
        }

        if ($send) {
            switch ($channel) {
                case 'sms':
                    $response['attempted']['sms'] = $this->sendSMSByProvider($smsprovider, $status, $customerPhone, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentIdToUse, $userId, $bundledPaidFlag, $awb_number);
                    $response['attempted']['sms']['phone'] = $customerPhone;
                    break;

                case 'whatsapp':
                    $response['attempted']['whatsapp'] = $this->sendWhatsAppByProvider($whatsappprovider, $status, $customerPhone, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentIdToUse, $userId, $bundledPaidFlag, $awb_number);
                    $response['attempted']['whatsapp']['orderid'] = $orderId;
                    break;

                case 'email':
                    $response['attempted']['email'] = $this->sendEmailByProvider($emailprovider, $status, $customerPhone, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentIdToUse, $userId, $bundledPaidFlag, $awb_number);
                    break;

                case 'ivr':
                    $response['attempted']['ivr'] = $this->sendIVRByProvider($ivrprovider, $status, $customerPhone, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentIdToUse, $userId, $bundledPaidFlag, $awb_number);
                    break;
            }
        } else {
            $response['skipped'][] = $channel;
        }
    }

    return $response;
}

private function isAnyStatusCaptured($orderId, $channel, array $statuses)
{
    if (empty($statuses)) return false;

    $this->CI->db->where('order_id', $orderId)
        ->where('channel_name', $channel)
        ->where_in('status', $statuses)
         ->where('bundled_paid', 1)
        ->where('is_captured', 1);

    return $this->CI->db->get('tbl_notification_responses')->num_rows() > 0;
}

private function sendSMSByProvider($provider, $status, $customerPhone, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentIdToUse, $userId, $bundledPaidFlag, $awb_number)
{
    switch ($provider) {
        case 'msg91':
            return $this->sendSMS_MSG91($provider, $status, $customerPhone, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentIdToUse, $userId, $bundledPaidFlag, $awb_number);
        case 'exotel':
            return $this->sendSMS_Exotel($provider, $status, $customerPhone, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentIdToUse, $userId, $bundledPaidFlag, $awb_number);
        default:
            return ['error' => true, 'message' => "SMS: Unsupported provider $provider"];
    }
}

private function sendWhatsAppByProvider($provider, $status, $customerPhone, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentIdToUse, $userId, $bundledPaidFlag, $awb_number)
{
    switch ($provider) {
        case 'msg91':
            return $this->sendWhatsApp_MSG91($provider, $status, $customerPhone, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentIdToUse, $userId, $bundledPaidFlag, $awb_number);
        case 'exotel':
            return $this->sendWhatsApp_Exotel($provider, $status, $customerPhone, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentIdToUse, $userId, $bundledPaidFlag, $awb_number);
        default:
            return ['error' => true, 'message' => "WhatsApp: Unsupported provider $provider"];
    }
}

private function sendEmailByProvider($provider, $status, $customerPhone, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentIdToUse, $userId, $bundledPaidFlag, $awb_number)
{
    switch ($provider) {
        case 'msg91':
            return $this->sendEmail_MSG91($provider, $status, $customerPhone, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentIdToUse, $userId, $bundledPaidFlag, $awb_number);
        case 'exotel':
            return $this->sendEmail_Exotel($provider, $status, $customerPhone, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentIdToUse, $userId, $bundledPaidFlag, $awb_number);
        default:
            return ['error' => true, 'message' => "Email: Unsupported provider $provider"];
    }
}

private function sendIVRByProvider($provider, $status, $customerPhone, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentIdToUse, $userId, $bundledPaidFlag, $awb_number)
{
    switch ($provider) {
        case 'msg91':
            return $this->sendIVR_MSG91($provider, $status, $customerPhone, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentIdToUse, $userId, $bundledPaidFlag, $awb_number);
        case 'exotel':
            return $this->sendIVR_Exotel($provider, $status, $customerPhone, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentIdToUse, $userId, $bundledPaidFlag, $awb_number);
        default:
            return ['error' => true, 'message' => "Email: Unsupported provider $provider"];
    }
}




private function sendSMS_MSG91($provider, $status, $mobile, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentId, $userId, $bundledPaidFlag, $awb_number)
{
   
    
    $url = "https://control.msg91.com/api/v5/flow";
    $authKey = "434505ATvL0aSVRtef67ebdcabP1"; 

    $payload = [];

    switch ($status) {
        case 'in transit':
            $payload = [
                "template_id" => "6880d8e4d6fc0579dc073892",
                "recipients" => [[
                    "mobiles" => $mobile,
                    "var1" => $orderId,
                    "var2" => $trackingUrl,
                    "var3" => $companyName
                ]]
            ];
            break;

        case 'out for delivery':
            $payload = [
                "template_id" => "68809a63d6fc0551e606b4e2",
                "recipients" => [[
                    "mobiles" => $mobile,
                    "var1" => $orderId,
                    "var2" => $sellerMobile,
                    "var3" => $companyName
                ]]
            ];
            break;

        case 'pending pickup':
            $payload = [
                "template_id" => "688090f0d6fc050de7789d42",
                "recipients" => [[
                    "mobiles" => $mobile,
                    "var1" => $orderId,
                    "var2" => $companyName
                ]]
            ];
            break;

        case 'delivered':
            $payload = [
                "template_id" => "6884c0a2d6fc057c05081642",
                "recipients" => [[
                    "mobiles" => $mobile,
                    "var1" => $orderId,
                    "var2" => $sellerMobile,
                    "var3" => $companyName
                ]]
            ];
            break;

        case 'rto in transit':
            $payload = [
                "template_id" => "6884b9a0d6fc055d2a775b14",
                "recipients" => [[
                    "mobiles" => $mobile,
                    "var1" => $orderId,
                    "var2" => $sellerMobile,
                    "var3" => $companyName
                ]]
            ];
            break;
        
        case 'exception':
            $payload = [
                "template_id" => "68997a1958a5fb5d343db482",
                "recipients" => [[
                    "mobiles" => $mobile,
                    "var1" => $orderId,
                    "var2" => $sellerMobile,
                    "var3" => $companyName
                ]]
            ];
            break;

        default:
            return 'SMS: no template for status';
    }

    try {
        $result = $this->sendCurlRequestMSG91($url, $authKey, $payload);
        $httpCode = $result['http_code'] ?? null;
        $responseBody = $result['response'] ?? null;

        if ($httpCode !== 200) {
            return [
                'error' => true,
                'message' => 'SMS: Failed with HTTP status ' . $httpCode,
                'details' => $responseBody
            ];
        }

        $decoded = json_decode($responseBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => true,
                'message' => 'SMS: Invalid JSON in response',
                'details' => $responseBody
            ];
        }

        $requestId = $decoded['message'] ?? null;
        if (!$requestId) {
            return [
                'error' => true,
                'message' => 'SMS: No request ID in response',
                'details' => $decoded
            ];
        }

        // Log response to DB
        $this->CI->db->insert('tbl_notification_responses', [
            'user_id' => $userId,
            'request_id' => $requestId,
            'channel_name' => "sms",
            'shipment_id' => $shipmentId,
            'order_id' => $orderIdInput,
            'order_number' => $orderId,
            'awb_number' => $awb_number,
            'status' => $status,
            'bundled_paid' => $bundledPaidFlag,
            'is_captured' => 0,
            'provider' => $provider,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return [
            'http_code' => $httpCode,
            'request_id' => $requestId,
            'data' => $decoded
        ];
    } catch (Exception $e) {
        return [
            'error' => true,
            'message' => 'SMS: Exception occurred',
            'details' => $e->getMessage()
        ];
    }
}



private function sendWhatsApp_MSG91($provider, $status, $mobile, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentId, $userId, $bundledPaidFlag, $awb_number)
{
    

    $url = "https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/";
    $authKey = "434505ATvL0aSVRtef67ebdcabP1";

    $payload = [];

    switch (strtolower($status)) {
        case 'in transit':
            $payload = [
                "integrated_number" => "919266426868",
                "content_type" => "template",
                "payload" => [
                    "messaging_product" => "whatsapp",
                    "type" => "template",
                    "template" => [
                        "name" => "shipped_in_transit",
                        "language" => ["code" => "en", "policy" => "deterministic"],
                        "namespace" => "23453da1_7ab2_4319_8b1c_bcb138d80104",
                        "to_and_components" => [[
                            "to" => [$mobile],
                            "components" => [
                                "body_1" => ["type" => "text", "value" => $orderId],
                                "body_2" => ["type" => "text", "value" => $trackingUrl],
                                "body_3" => ["type" => "text", "value" => $companyName]
                            ]
                        ]]
                    ]
                ]
            ];
            break;

        case 'delivered':
            $payload = [
                "integrated_number" => "919266426868",
                "content_type" => "template",
                "payload" => [
                    "messaging_product" => "whatsapp",
                    "type" => "template",
                    "template" => [
                        "name" => "delivered_",
                        "language" => ["code" => "en", "policy" => "deterministic"],
                        "namespace" => "23453da1_7ab2_4319_8b1c_bcb138d80104",
                        "to_and_components" => [[
                            "to" => [$mobile],
                            "components" => [
                                "body_1" => ["type" => "text", "value" => $orderId],
                                "body_2" => ["type" => "text", "value" => $trackingUrl],
                                "body_3" => ["type" => "text", "value" => $companyName]
                            ]
                        ]]
                    ]
                ]
            ];
            break;

        case 'rto in transit':
            $payload = [
                "integrated_number" => "919266426868",
                "content_type" => "template",
                "payload" => [
                    "messaging_product" => "whatsapp",
                    "type" => "template",
                    "template" => [
                        "name" => "rto",
                        "language" => ["code" => "en_US", "policy" => "deterministic"],
                        "namespace" => "23453da1_7ab2_4319_8b1c_bcb138d80104",
                        "to_and_components" => [[
                            "to" => [$mobile],
                            "components" => [
                                "body_1" => ["type" => "text", "value" => $orderId],
                                "body_2" => ["type" => "text", "value" => $sellerMobile],
                                "body_3" => ["type" => "text", "value" => $companyName]
                            ]
                        ]]
                    ]
                ]
            ];
            break;

        case 'out for delivery':
            $payload = [
                "integrated_number" => "919266426868",
                "content_type" => "template",
                "payload" => [
                    "messaging_product" => "whatsapp",
                    "type" => "template",
                    "template" => [
                        "name" => "out_for_delivery_cod",
                        "language" => ["code" => "en", "policy" => "deterministic"],
                        "namespace" => "23453da1_7ab2_4319_8b1c_bcb138d80104",
                        "to_and_components" => [[
                            "to" => [$mobile],
                            "components" => [
                                "body_1" => ["type" => "text", "value" => $orderId],
                                "body_2" => ["type" => "text", "value" => $sellerMobile],
                                "body_3" => ["type" => "text", "value" => $companyName]
                            ]
                        ]]
                    ]
                ]
            ];
            break;

        case 'pending pickup':
            $payload = [
                "integrated_number" => "919266426868",
                "content_type" => "template",
                "payload" => [
                    "messaging_product" => "whatsapp",
                    "type" => "template",
                    "template" => [
                        "name" => "order_packed",
                        "language" => ["code" => "en", "policy" => "deterministic"],
                        "namespace" => "23453da1_7ab2_4319_8b1c_bcb138d80104",
                        "to_and_components" => [[
                            "to" => [$mobile],
                            "components" => [
                                "body_1" => ["type" => "text", "value" => $orderId],
                                "body_2" => ["type" => "text", "value" => $companyName]
                            ]
                        ]]
                    ]
                ]
            ];
            break;

        case 'new':
            $payload = [
                "integrated_number" => "919266426868",
                "content_type" => "template",
                "payload" => [
                    "messaging_product" => "whatsapp",
                    "type" => "template",
                    "template" => [
                        "name" => "order_verification",
                        "language" => ["code" => "en", "policy" => "deterministic"],
                        "namespace" => "23453da1_7ab2_4319_8b1c_bcb138d80104",
                        "to_and_components" => [[
                            "to" => [$mobile],
                            "components" => [
                                "body_1" => ["type" => "text", "value" => "Customer"],
                                "body_2" => ["type" => "text", "value" => $orderId],
                                "body_3" => ["type" => "text", "value" => $companyName],
                                "body_4" => ["type" => "text", "value" => $companyName]
                            ]
                        ]]
                    ]
                ]
            ];
            break;

        case 'confirmation acknowledgement':
            $payload = [
                "integrated_number" => "919266426868",
                "content_type" => "template",
                "payload" => [
                    "messaging_product" => "whatsapp",
                    "type" => "template",
                    "template" => [
                        "name" => "order_verified_acknowledgment",
                        "language" => ["code" => "en", "policy" => "deterministic"],
                        "namespace" => "23453da1_7ab2_4319_8b1c_bcb138d80104",
                        "to_and_components" => [[
                            "to" => [$mobile],
                            "components" => [
                                "body_1" => ["type" => "text", "value" => $orderId],
                                "body_2" => ["type" => "text", "value" => $companyName]
                            ]
                        ]]
                    ]
                ]
            ];
            break;

        case 'exception':
            $payload = [
                "integrated_number" => "919266426868",
                "content_type" => "template",
                "payload" => [
                    "messaging_product" => "whatsapp",
                    "type" => "template",
                    "template" => [
                        "name" => "delivery_failed_ndr",
                        "language" => ["code" => "en", "policy" => "deterministic"],
                        "namespace" => "23453da1_7ab2_4319_8b1c_bcb138d80104",
                        "to_and_components" => [[
                            "to" => [$mobile],
                            "components" => [
                                "body_1" => ["type" => "text", "value" => $companyName],
                                "body_2" => ["type" => "text", "value" => $orderId],
                                "body_3" => ["type" => "text", "value" => $sellerMobile]
                            ]
                        ]]
                    ]
                ]
            ];
            break;

        default:
            return ['success' => false, 'error' => 'WhatsApp: no template for this status'];
    }

    $result = $this->sendCurlRequestMSG91($url, $authKey, $payload);

    
    if (!isset($result['http_code']) || !isset($result['response'])) {
        return [
            'success' => false,
            'error' => 'WhatsApp: Invalid or missing cURL response',
            'result' => $result
        ];
    }

   
    $decoded = json_decode($result['response'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'success' => false,
            'error' => 'WhatsApp: Invalid JSON response',
            'raw_response' => $result['response'],
            'json_error' => json_last_error_msg(),
            'http_code' => $result['http_code']
        ];
    }

    $requestId = $decoded['request_id'] ?? null;

    if ($requestId) {
        $this->CI->db->insert('tbl_notification_responses', [
            'user_id' => $userId,
            'request_id' => $requestId,
            'channel_name' => "whatsapp",
            'shipment_id' => $shipmentId,
            'order_id' => $orderIdInput,
            'order_number' => $orderId,
            'awb_number' => $awb_number,
            'status' => $status,
            'bundled_paid' => $bundledPaidFlag,
            'is_captured' => 0,
            'provider' => $provider,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return [
            'success' => true,
            'http_code' => $result['http_code'],
            'data' => $decoded
        ];
    }

    
    return [
        'success' => false,
        'http_code' => $result['http_code'],
        'error' => $decoded['message'] ?? 'Unknown error from Msg91',
        'data' => $decoded
    ];
}



private function sendEmail_MSG91($provider, $status, $mobile, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentId, $userId, $bundledPaidFlag, $awb_number)
{
    

    $order = $this->CI->db->get_where('tbl_orders', ['id' => $orderIdInput])->row();
    if (!$order || empty($order->shipping_email)) {
        return ['success' => false, 'message' => 'Email: customer email not found'];
    }

    $email = trim($order->shipping_email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Email: invalid email address'];
    }

    $customerName = !empty($order->shipping_name) ? $order->shipping_name : 'Customer';
    $templateId = null;
    $variables = [];

    switch (strtolower($status)) {
        case 'in transit':
            $templateId = 'shipped_in_transit_2';
            $variables = [
                "VAR1" => $companyName,
                "VAR2" => $trackingUrl,
                "VAR3" => $customerName,
                "VAR4" => $orderId,
            ];
            break;

        case 'out for delivery':
            $templateId = 'out_for_delivery_2';
            $variables = [
                "VAR1" => $customerName,
                "VAR2" => $orderId,
                "VAR3" => $companyName
            ];
            break;

        case 'delivered':
            $templateId = 'delivered_2';
            $variables = [
                "VAR1" => $customerName,
                "VAR2" => $orderId,
                "VAR3" => 'custom url',
                "VAR4" => $companyName,
            ];
            break;

        case 'pending pickup':
            $templateId = 'order_packed';
            $variables = [
                "VAR1" => $companyName,
                "VAR2" => $orderId,
                "VAR3" => $customerName,
            ];
            break;

        case 'rto in transit':
            $templateId = 'rto_in_transit';
            $variables = [
                "VAR1" => $customerName,
                "VAR2" => $orderId,
                "VAR3" => $sellerMobile,
                "VAR4" => $companyName,
            ];
            break;
        
        case 'exception':
            $templateId = 'delivery_failed_ndr';
            $variables = [
                "VAR1" => $customerName,
                "VAR2" => $orderId,
                "VAR3" => $sellerMobile,
                "VAR4" => $companyName,
            ];
            break;

        default:
            return ['success' => false, 'message' => 'Email: no template for this status'];
    }

    $url = "https://api.msg91.com/api/v5/email/send";
    $authKey = "434505ATvL0aSVRtef67ebdcabP1";

    $payload = [
        "recipients" => [[
            "to" => [[
                "email" => $email,
                "name" => $customerName
            ]],
            "variables" => $variables
        ]],
        "from" => [
            "email" => "daakit@notification.daakit.com"
        ],
        "domain" => "notification.daakit.com",
        "template_id" => $templateId
    ];

    $result = $this->sendCurlRequestMSG91($url, $authKey, $payload);
    $decoded = json_decode($result['response'], true);
    $httpCode = $result['http_code'] ?? 0;

    if ($httpCode !== 200) {
        return [
            'success' => false,
            'message' => 'Email API call failed',
            'http_code' => $httpCode,
            'response' => $decoded
        ];
    }

    $requestId = $decoded['data']['unique_id'] ?? null;

    if (!$requestId) {
        return [
            'success' => false,
            'message' => 'Email sent but no request ID returned',
            'http_code' => $httpCode,
            'response' => $decoded
        ];
    }

    
    $this->CI->db->insert('tbl_notification_responses', [
        'user_id' => $order->user_id,
        'request_id' => $requestId,
        'channel_name' => "email",
        'shipment_id' => $shipmentId,
        'order_id' => $orderIdInput,
        'order_number' => $orderId,
        'awb_number' => $awb_number,
        'status' => $status,
        'bundled_paid' => $bundledPaidFlag,
        'is_captured' => 0,
        'provider' => $provider,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    return [
        'success' => true,
        'message' => 'Email sent successfully',
        'http_code' => $httpCode,
        'request_id' => $requestId
    ];
}



private function sendIVR_MSG91($provider, $status, $mobile, $orderId)
{
    
    $info = "IVR triggered (mock) for $mobile, status: $status";
    log_message('info', $info);
    return $info;
}

private function sendIVR_Exotel($provider, $status, $mobile, $orderId, $orderIdInput, $companyName, $trackingUrl, $sellerMobile, $shipmentId, $userId, $bundledPaidFlag, $awb_number)
{
    
    $orderQuery = $this->CI->db->select('shipping_fname')
        ->from('tbl_orders')
        ->where('id', $orderIdInput)
        ->get();

    if ($orderQuery->num_rows() === 0) {
        return [
            'error' => true,
            'message' => "Order not found for orderId: $orderId"
        ];
    }

    $orderRow = $orderQuery->row();
    $customerName = $orderRow->shipping_fname;

   
    $itemsQuery = $this->CI->db->select('product_name')
        ->from('tbl_order_products')
        ->where('order_id', $orderIdInput)
        ->get();

    $products = [];
    foreach ($itemsQuery->result() as $row) {
        $products[] = $row->product_name;
    }
    $productNames = implode(',', $products);

   
    $customField = "{$customerName}|{$productNames}|{$orderId}|{$companyName}";

    
    $url     = "https://api.exotel.com/v1/Accounts/daakit1/Calls/connect.json";
    $apiKey  = "f5567a11621972daba5e0c098356aa9337ec30d3bf5f8b24";   
    $apiToken= "a04e9a0fb55cf328dc9457d621e49d7cb190b82e48ef3cdf";

    
    $exoMLUrl = null;

    switch (strtolower($status)) {
        case 'new':
            $exoMLUrl = "http://api.exotel.com/Exotel/exoml/start_voice/1070885";
            break;

        default:
            return [
                'error' => true,
                'message' => "IVR: No ExoML flow mapped for status '{$status}'"
            ];
    }

    
    $postData = [
        'From'          => $mobile,
        'CallerId'      => "01141202484",
        'Url'           => $exoMLUrl,
        'CustomField'   => $customField,
        'StatusCallback'=> 'https://app.daakit.com/index.php/api/exotelwebhook/exotelStatusWebhook'
    ];

  
    $result = $this->sendCurlRequestExotel($url, $apiKey, $apiToken, $postData);
    $httpCode = $result['http_code'] ?? null;
    $responseBody = $result['response'] ?? null;

    if ($httpCode !== 200) {
        return [
            'error' => true,
            'message' => "IVR call failed with status $httpCode",
            'details' => $responseBody
        ];
    }

    $decoded = json_decode($responseBody, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'error' => true,
            'message' => "IVR: Invalid JSON in response",
            'details' => $responseBody
        ];
    }

    $callSid = $decoded['Call']['Sid'] ?? null;
    if (!$callSid) {
        return [
            'error' => true,
            'message' => "IVR: No Call SID in response",
            'details' => $decoded
        ];
    }

   
    $this->CI->db->insert('tbl_notification_responses', [
        'user_id'     => $userId,
        'request_id'  => $callSid,
        'channel_name'=> 'ivr',
        'shipment_id' => $shipmentId,
        'order_id'    => $orderIdInput,
        'order_number'=> $orderId,
        'awb_number'  => $awb_number,
        'status'      => $status,
        'bundled_paid'=> $bundledPaidFlag,
        'is_captured' => 0,
        'provider' => $provider,
        'created_at'  => date('Y-m-d H:i:s')
    ]);

    return [
        'http_code' => $httpCode,
        'call_sid'  => $callSid,
        'data'      => $decoded
    ];
}



private function sendCurlRequestMSG91($url, $authKey, $payload)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'authkey: ' . $authKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    log_message('info', 'Notification sent. HTTP ' . $httpCode . ': ' . $response);
    return ['http_code' => $httpCode, 'response' => $response];
}

private function sendCurlRequestExotel($url, $apiKey, $apiToken, $payload)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:$apiToken");
    $response = curl_exec($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'http_code' => $httpCode,
        'response'  => $response
    ];
}



}
