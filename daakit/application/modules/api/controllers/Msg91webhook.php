<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';

class Msg91webhook extends RestController
{
    public function __construct()
    {
        parent::__construct('rest_api');
        
    }

// public function msg91Webhookwhatsappoutbound_post()
// {
//     $input = json_decode(file_get_contents("php://input"), true);

//     if (empty($input['request_id']) || empty($input['status'])) {
//         return $this->output
//             ->set_status_header(400)
//             ->set_output(json_encode(['error' => 'Invalid payload']));
//     }

//     $requestId = $input['request_id'];
//     $status = strtolower($input['status']);
//     $messageUuid = $input['message_uuid'] ?? null;

//     if ($status !== 'delivered') {
//         return $this->output
//             ->set_status_header(200)
//             ->set_output(json_encode(['message' => 'Status not handled']));
//     }

//     $response = $this->db->get_where('tbl_notification_responses', [
//         'request_id' => $requestId
//     ])->row();

//     if (!$response) {
//         return $this->output
//             ->set_status_header(404)
//             ->set_output(json_encode(['error' => 'Notification response not found']));
//     }

//     if ((int)$response->is_captured === 1) {
//         return $this->output
//             ->set_status_header(200)
//             ->set_output(json_encode(['message' => 'Already captured']));
//     }

//     $user = $this->db->get_where('tbl_users', ['id' => $response->user_id])->row();
//     if (!$user) {
//         return $this->output
//             ->set_status_header(404)
//             ->set_output(json_encode(['error' => 'User not found']));
//     }

//     $plan = strtolower($user->communication_plan ?? '');
//     $channel = $response->channel_name;
//     $price = 0;
//     $bundledPaidFlag = 0;

//     $orderSource = '';
//     if (!empty($response->order_id)) {
//         $order = $this->db->get_where('tbl_orders', ['id' => $response->order_id])->row();
//         $orderSource = strtolower($order->order_source ?? '');
//     } elseif (!empty($response->shipment_id)) {
//         $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
//         if ($shipment && !empty($shipment->order_id)) {
//             $order = $this->db->get_where('tbl_orders', ['id' => $shipment->order_id])->row();
//             $orderSource = strtolower($order->order_source ?? '');
//         }
//     }

//     if ($plan === 'individual') {
//         // $priceRow = $this->db->get_where('tbl_communication_individual_price', ['status' => strtolower($response->status)])->row();
//         // $basePrice = $priceRow ? (float)$priceRow->$channel : 0;
//         // $price = round($basePrice * 1.18, 2); // Add 18% GST
//         $alreadyBundledPaid = $this->db->where([
//         'order_id'      => $response->order_id,
//         'channel_name'  => $channel,
//         'bundled_paid'  => 1,
//         'is_captured'   => 1
//       ])->get('tbl_notification_responses')->num_rows() > 0;
//     if ($alreadyBundledPaid) {
//         $price = 0;
//         $bundledPaidFlag = 1;
//     } else {
//         $priceRow = $this->db->get_where('tbl_communication_individual_price', [
//             'status' => strtolower($response->status)
//         ])->row();
//         $basePrice = $priceRow ? (float)$priceRow->$channel : 0;
//         $price = round($basePrice * 1.18, 2); // GST
//     }
//     } elseif ($plan === 'bundled') {
//         $specific = array_filter([
//             $user->communication_specific1 ?? null,
//             $user->communication_specific2 ?? null,
//             $user->communication_specific3 ?? null,
//             $user->communication_specific4 ?? null
//         ]);

//         if (!empty($specific) && in_array($channel, $specific)) {
//     $orderIdToCheck = $response->order_id;

//     if (empty($orderIdToCheck) && !empty($response->shipment_id)) {
//         $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
//         $orderIdToCheck = $shipment ? $shipment->order_id : null;
//     }

//     if (!empty($orderIdToCheck)) {
//         $statusLower = strtolower($response->status);

//         // Determine valid initial statuses
//         $initialStatuses = [];
//         if (in_array($channel, ['whatsapp', 'sms', 'email'])) {
//             if ($orderSource === 'api') {
//                 $initialStatuses = $channel === 'whatsapp' ? ['new', 'confirmation acknowledgement', 'pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception'] : ['pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception'];
//             } else {
//                 $initialStatuses = $channel === 'whatsapp' ? ['new', 'confirmation acknowledgement', 'pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception'] : ['pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception'];
//             }
//         }

//         $bundleRow = $this->db->get('tbl_communication_bundeled_price')->row();
//         $basePrice = $bundleRow && isset($bundleRow->$channel) ? (float)$bundleRow->$channel : 0;

//         if (in_array($statusLower, $initialStatuses)) {
//                     $alreadyCaptured = $this->db
//                 ->where('order_id', $orderIdToCheck)
//                 ->where('channel_name', $channel)
//                 ->where_in('status', $initialStatuses)
//                 ->where('bundled_paid', 1)
//                 ->where('is_captured', 1)
//                 ->limit(1)
//                 ->get('tbl_notification_responses')
//                 ->num_rows() > 0;

//             $price = $alreadyCaptured ? 0 : round($basePrice * 1.18, 2); // Apply GST
//             $bundledPaidFlag = 1;
//         } else {
//             // Check if any initial status already captured
//                     $alreadyCaptured = $this->db
//                 ->where('order_id', $orderIdToCheck)
//                 ->where('channel_name', $channel)
//                 ->where_in('status', $initialStatuses)
//                 ->where('bundled_paid', 1)
//                 ->where('is_captured', 1)
//                 ->limit(1)
//                 ->get('tbl_notification_responses')
//                 ->num_rows() > 0;

//             $price = $alreadyCaptured ? 0 : round($basePrice * 1.18, 2); // Apply GST
//             $bundledPaidFlag = 1;
//         }
//     }
// }

//     }

//     if ($user->wallet_balance < $price) {
//         return $this->output
//             ->set_status_header(400)
//             ->set_output(json_encode(['error' => 'Insufficient balance']));
//     }

//     // Start DB transaction
//     $this->db->trans_start();

//     if ($price > 0) {
//         $balanceBefore = $user->wallet_balance;
//         $balanceAfter = $balanceBefore - $price;

//         // Update user balance
//         $this->db->set('wallet_balance', "wallet_balance - $price", false)
//             ->where('id', $user->id)
//             ->update('tbl_users');

//         // Get order number
//         $orderNumber = '';
//         if (!empty($response->order_id)) {
//             $order = $this->db->get_where('tbl_orders', ['id' => $response->order_id])->row();
//             if ($order) {
//                 $orderNumber = !empty($order->api_order_id) ? $order->api_order_id : $order->order_no;
//             }
//         } elseif (!empty($response->shipment_id)) {
//             $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
//             if ($shipment && !empty($shipment->order_id)) {
//                 $order = $this->db->get_where('tbl_orders', ['id' => $shipment->order_id])->row();
//                 if ($order) {
//                     $orderNumber = !empty($order->api_order_id) ? $order->api_order_id : $order->order_no;
//                 }
//             }
//         }

//         // Insert wallet history
//         $this->db->insert('tbl_communication_wallet_history', [
//             'user_id' => $user->id,
//             'txn_for' => 'whatsapp',
//             'txn_ref' => $response->status,
//             'ref_id' => $response->order_id,
//             'balance_before' => number_format($balanceBefore, 2, '.', ''),
//             'amount' => number_format($price, 2, '.', ''),
//             'balance_after' => number_format($balanceAfter, 2, '.', ''),
//             'type' => 'debit',
//             'request_id' => $response->request_id,
//             'pack_type' => $plan,
//             'notes' => 'Communication charge for ' . strtoupper($channel) . ' - ' . $response->status,
//             'created' => time()
//         ]);
//     }

//     // Mark message as captured
//     $this->db->set([
//         'is_captured' => 1,
//         'message_uuid' => $messageUuid,
//         'bundled_paid' => $bundledPaidFlag
//     ])->where('request_id', $requestId)
//       ->update('tbl_notification_responses');

//     // Complete transaction
//     $this->db->trans_complete();

//     if ($this->db->trans_status() === FALSE) {
//         return $this->output
//             ->set_status_header(500)
//             ->set_output(json_encode(['error' => 'Database transaction failed']));
//     }

//     return $this->output
//         ->set_status_header(200)
//         ->set_output(json_encode(['message' => 'Status captured and balance deducted']));
// } 

// public function msg91Webhookwhatsappoutbound_post()
// {
//     $input = json_decode(file_get_contents("php://input"), true);

//     if (empty($input['request_id']) || empty($input['status'])) {
//         return $this->output
//             ->set_status_header(400)
//             ->set_output(json_encode(['error' => 'Invalid payload']));
//     }

//     $requestId = $input['request_id'];
//     $status = strtolower($input['status']);
//     $messageUuid = $input['message_uuid'] ?? null;

//     if ($status !== 'sent') {
//         return $this->output
//             ->set_status_header(200)
//             ->set_output(json_encode(['message' => 'Status not handled']));
//     }

//     // 🔒 Start transaction for row-level locking
//     $this->db->trans_start();

//     // Lock the notification row so no other process can modify it until transaction ends
//     $response = $this->db->query(
//         "SELECT * FROM tbl_notification_responses WHERE request_id = ? FOR UPDATE",
//         [$requestId]
//     )->row();

//     if (!$response) {
//         $this->db->trans_complete();
//         return $this->output
//             ->set_status_header(404)
//             ->set_output(json_encode(['error' => 'Notification response not found']));
//     }

//     if ((int)$response->is_captured === 1) {
//         $this->db->trans_complete();
//         return $this->output
//             ->set_status_header(200)
//             ->set_output(json_encode(['message' => 'Already captured']));
//     }

//     // Lock the user row too — avoids race condition on wallet balance
//     $user = $this->db->query(
//         "SELECT * FROM tbl_users WHERE id = ? FOR UPDATE",
//         [$response->user_id]
//     )->row();

//     if (!$user) {
//         $this->db->trans_complete();
//         return $this->output
//             ->set_status_header(404)
//             ->set_output(json_encode(['error' => 'User not found']));
//     }

//     $plan = strtolower($user->communication_plan ?? '');
//     $channel = $response->channel_name;
//     $price = 0;
//     $bundledPaidFlag = 0;

//     $orderSource = '';
//     if (!empty($response->order_id)) {
//         $order = $this->db->get_where('tbl_orders', ['id' => $response->order_id])->row();
//         $orderSource = strtolower($order->order_source ?? '');
//     } elseif (!empty($response->shipment_id)) {
//         $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
//         if ($shipment && !empty($shipment->order_id)) {
//             $order = $this->db->get_where('tbl_orders', ['id' => $shipment->order_id])->row();
//             $orderSource = strtolower($order->order_source ?? '');
//         }
//     }

//     // 🟢 Your existing logic remains untouched below
//     if ($plan === 'individual') {
//         $alreadyBundledPaid = $this->db->where([
//             'order_id'      => $response->order_id,
//             'channel_name'  => $channel,
//             'bundled_paid'  => 1,
//             'is_captured'   => 1
//         ])->get('tbl_notification_responses')->num_rows() > 0;

//         if ($alreadyBundledPaid) {
//             $price = 0;
//             $bundledPaidFlag = 1;
//         } else {
//             $priceRow = $this->db->get_where('tbl_communication_individual_price', [
//                 'status' => strtolower($response->status)
//             ])->row();
//             $basePrice = $priceRow ? (float)$priceRow->$channel : 0;
//             $price = round($basePrice * 1.18, 2); // GST
//         }
//     } elseif ($plan === 'bundled') {
//         $specific = array_filter([
//             $user->communication_specific1 ?? null,
//             $user->communication_specific2 ?? null,
//             $user->communication_specific3 ?? null,
//             $user->communication_specific4 ?? null
//         ]);

//         if (!empty($specific) && in_array($channel, $specific)) {
//             $orderIdToCheck = $response->order_id;

//             if (empty($orderIdToCheck) && !empty($response->shipment_id)) {
//                 $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
//                 $orderIdToCheck = $shipment ? $shipment->order_id : null;
//             }

//             if (!empty($orderIdToCheck)) {
//                 $statusLower = strtolower($response->status);

//                 $initialStatuses = [];
//                 if (in_array($channel, ['whatsapp', 'sms', 'email'])) {
//                     if ($orderSource === 'api') {
//                         $initialStatuses = $channel === 'whatsapp'
//                             ? ['new', 'confirmation acknowledgement', 'pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception']
//                             : ['pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception'];
//                     } else {
//                         $initialStatuses = $channel === 'whatsapp'
//                             ? ['new', 'confirmation acknowledgement', 'pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception']
//                             : ['pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception'];
//                     }
//                 }

//                 $bundleRow = $this->db->get('tbl_communication_bundeled_price')->row();
//                 $basePrice = $bundleRow && isset($bundleRow->$channel) ? (float)$bundleRow->$channel : 0;

//                 $alreadyCaptured = $this->db
//                     ->where('order_id', $orderIdToCheck)
//                     ->where('channel_name', $channel)
//                     ->where_in('status', $initialStatuses)
//                     ->where('bundled_paid', 1)
//                     ->where('is_captured', 1)
//                     ->limit(1)
//                     ->get('tbl_notification_responses')
//                     ->num_rows() > 0;

//                 $price = $alreadyCaptured ? 0 : round($basePrice * 1.18, 2); // Apply GST
//                 $bundledPaidFlag = 1;
//             }
//         }
//     }

//     if (($user->wallet_balance - $price) < $user->wallet_limit) {
//         $this->db->trans_complete();
//         return $this->output
//             ->set_status_header(400)
//             ->set_output(json_encode(['error' => 'Insufficient balance']));
//     }

//     if ($price > 0) {
//         $balanceBefore = $user->wallet_balance;
//         $balanceAfter = $balanceBefore - $price;

//         $this->db->set('wallet_balance', "wallet_balance - $price", false)
//             ->where('id', $user->id)
//             ->update('tbl_users');

//         $orderNumber = '';
//         if (!empty($response->order_id)) {
//             $order = $this->db->get_where('tbl_orders', ['id' => $response->order_id])->row();
//             if ($order) {
//                 $orderNumber = !empty($order->api_order_id) ? $order->api_order_id : $order->order_no;
//             }
//         } elseif (!empty($response->shipment_id)) {
//             $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
//             if ($shipment && !empty($shipment->order_id)) {
//                 $order = $this->db->get_where('tbl_orders', ['id' => $shipment->order_id])->row();
//                 if ($order) {
//                     $orderNumber = !empty($order->api_order_id) ? $order->api_order_id : $order->order_no;
//                 }
//             }
//         }

//         $this->db->insert('tbl_communication_wallet_history', [
//             'user_id' => $user->id,
//             'txn_for' => 'whatsapp',
//             'txn_ref' => $response->status,
//             'ref_id' => $response->order_id,
//             'balance_before' => number_format($balanceBefore, 2, '.', ''),
//             'amount' => number_format($price, 2, '.', ''),
//             'balance_after' => number_format($balanceAfter, 2, '.', ''),
//             'type' => 'debit',
//             'request_id' => $response->request_id,
//             'pack_type' => $plan,
//             'notes' => 'Communication charge for ' . strtoupper($channel) . ' - ' . $response->status,
//             'created' => time()
//         ]);
//     }

//     $this->db->set([
//         'is_captured' => 1,
//         'message_uuid' => $messageUuid,
//         'bundled_paid' => $bundledPaidFlag
//     ])->where('request_id', $requestId)
//       ->update('tbl_notification_responses');

//     $this->db->trans_complete();

//     if ($this->db->trans_status() === FALSE) {
//         return $this->output
//             ->set_status_header(500)
//             ->set_output(json_encode(['error' => 'Database transaction failed']));
//     }

//     return $this->output
//         ->set_status_header(200)
//         ->set_output(json_encode(['message' => 'Status captured and balance deducted']));
// }


// public function msg91Webhookwhatsappoutbound_post()
// {
//     $input = json_decode(file_get_contents("php://input"), true);

//     if (empty($input['request_id']) || empty($input['status'])) {
//         return $this->output
//             ->set_status_header(400)
//             ->set_output(json_encode(['error' => 'Invalid payload']));
//     }

//     $requestId   = $input['request_id'];
//     $status      = strtolower($input['status']);
//     $messageUuid = $input['message_uuid'] ?? null;


//     $this->db->trans_start();

   
//     $response = $this->db->query(
//         "SELECT * FROM tbl_notification_responses WHERE request_id = ? FOR UPDATE",
//         [$requestId]
//     )->row();

//     if (!$response) {
//         $this->db->trans_complete();
//         return $this->output
//             ->set_status_header(404)
//             ->set_output(json_encode(['error' => 'Notification response not found']));
//     }

   
//     if ($status === 'delivered') {
//         $this->db->set([
//             'delivered_at' => isset($input['delivered_at']) 
//                     ? date("Y-m-d H:i:s", $input['delivered_at']) 
//                     : date("Y-m-d H:i:s"),
//             'delivery_status' => 'delivered'
//         ])->where('request_id', $requestId)
//           ->update('tbl_notification_responses');
//     }

//     if ($status === 'read') {
//         $this->db->set([
//            'read_at' => isset($input['read_at']) 
//                 ? date("Y-m-d H:i:s", $input['read_at']) 
//                 : date("Y-m-d H:i:s")

//         ])->where('request_id', $requestId)
//           ->update('tbl_notification_responses');
//     }

//     if ($status === 'sent') {
//         if ((int)$response->is_captured !== 1) {
//             // Lock the user row
//             $user = $this->db->query(
//                 "SELECT * FROM tbl_users WHERE id = ? FOR UPDATE",
//                 [$response->user_id]
//             )->row();

//             if (!$user) {
//                 $this->db->trans_complete();
//                 return $this->output
//                     ->set_status_header(404)
//                     ->set_output(json_encode(['error' => 'User not found']));
//             }

//             $plan = strtolower($user->communication_plan ?? '');
//             $channel = $response->channel_name;
//             $price = 0;
//             $bundledPaidFlag = 0;
//             $orderSource = '';

//             if (!empty($response->order_id)) {
//                 $order = $this->db->get_where('tbl_orders', ['id' => $response->order_id])->row();
//                 $orderSource = strtolower($order->order_source ?? '');
//             } elseif (!empty($response->shipment_id)) {
//                 $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
//                 if ($shipment && !empty($shipment->order_id)) {
//                     $order = $this->db->get_where('tbl_orders', ['id' => $shipment->order_id])->row();
//                     $orderSource = strtolower($order->order_source ?? '');
//                 }
//             }

//             if ($plan === 'individual') {
//                 $alreadyBundledPaid = $this->db->where([
//                     'order_id'      => $response->order_id,
//                     'channel_name'  => $channel,
//                     'bundled_paid'  => 1,
//                     'is_captured'   => 1
//                 ])->get('tbl_notification_responses')->num_rows() > 0;

//                 if ($alreadyBundledPaid) {
//                     $price = 0;
//                     $bundledPaidFlag = 1;
//                 } else {
//                     // $priceRow = $this->db->get_where('tbl_communication_individual_price', [
//                     //     'status' => strtolower($response->status)
//                     // ])->row();
//                      $priceRow = $this->db
//                         ->where('user_id', $response->user_id)
//                         ->where('status', strtolower($response->status))
//                         ->get('tbl_user_individual_price')
//                         ->row();

//                     if (!$priceRow) {
//                         $priceRow = $this->db
//                             ->get_where('tbl_communication_individual_price', ['status' => strtolower($response->status)])
//                             ->row();
//                     }
//                     $basePrice = $priceRow ? (float)$priceRow->$channel : 0;
//                     $price = round($basePrice * 1.18, 2); // GST
//                 }
//             } elseif ($plan === 'bundled') {
//                 $specific = array_filter([
//                     $user->communication_specific1 ?? null,
//                     $user->communication_specific2 ?? null,
//                     $user->communication_specific3 ?? null,
//                     $user->communication_specific4 ?? null
//                 ]);

//                 if (!empty($specific) && in_array($channel, $specific)) {
//                     $orderIdToCheck = $response->order_id;

//                     if (empty($orderIdToCheck) && !empty($response->shipment_id)) {
//                         $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
//                         $orderIdToCheck = $shipment ? $shipment->order_id : null;
//                     }

//                     if (!empty($orderIdToCheck)) {
//                         $statusLower = strtolower($response->status);

//                         $initialStatuses = [];
//                         if (in_array($channel, ['whatsapp', 'sms', 'email'])) {
//                             if ($orderSource === 'api') {
//                                 $initialStatuses = $channel === 'whatsapp'
//                                     ? ['new', 'confirmation acknowledgement', 'pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception']
//                                     : ['pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception'];
//                             } else {
//                                 $initialStatuses = $channel === 'whatsapp'
//                                     ? ['new', 'confirmation acknowledgement', 'pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception']
//                                     : ['pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception'];
//                             }
//                         }

//                         // $bundleRow = $this->db->get('tbl_communication_bundeled_price')->row();
//                          $bundleRow = $this->db
//                             ->where('user_id', $response->user_id)
//                             ->get('tbl_user_bundeled_price')
//                             ->row();

//                         if (!$bundleRow) {
//                             $bundleRow = $this->db
//                                 ->get('tbl_communication_bundeled_price')
//                                 ->row();
//                         }
//                         $basePrice = $bundleRow && isset($bundleRow->$channel) ? (float)$bundleRow->$channel : 0;

//                         $alreadyCaptured = $this->db
//                             ->where('order_id', $orderIdToCheck)
//                             ->where('channel_name', $channel)
//                             ->where_in('status', $initialStatuses)
//                             ->where('bundled_paid', 1)
//                             ->where('is_captured', 1)
//                             ->limit(1)
//                             ->get('tbl_notification_responses')
//                             ->num_rows() > 0;

//                         $price = $alreadyCaptured ? 0 : round($basePrice * 1.18, 2);
//                         $bundledPaidFlag = 1;
//                     }
//                 }
//             }

//             if (($user->wallet_balance - $price) < $user->wallet_limit) {
//                 $this->db->trans_complete();
//                 return $this->output
//                     ->set_status_header(400)
//                     ->set_output(json_encode(['error' => 'Insufficient balance']));
//             }

//             if ($price > 0) {
//                 $balanceBefore = $user->wallet_balance;
//                 $balanceAfter = $balanceBefore - $price;

//                 $this->db->set('wallet_balance', "wallet_balance - $price", false)
//                     ->where('id', $user->id)
//                     ->update('tbl_users');

//                 $orderNumber = '';
//                 if (!empty($response->order_id)) {
//                     $order = $this->db->get_where('tbl_orders', ['id' => $response->order_id])->row();
//                     if ($order) {
//                         $orderNumber = !empty($order->api_order_id) ? $order->api_order_id : $order->order_no;
//                     }
//                 } elseif (!empty($response->shipment_id)) {
//                     $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
//                     if ($shipment && !empty($shipment->order_id)) {
//                         $order = $this->db->get_where('tbl_orders', ['id' => $shipment->order_id])->row();
//                         if ($order) {
//                             $orderNumber = !empty($order->api_order_id) ? $order->api_order_id : $order->order_no;
//                         }
//                     }
//                 }

//                 $this->db->insert('tbl_communication_wallet_history', [
//                     'user_id'        => $user->id,
//                     'txn_for'        => 'whatsapp',
//                     'txn_ref'        => $response->status,
//                     'ref_id'         => $response->order_id,
//                     'balance_before' => number_format($balanceBefore, 2, '.', ''),
//                     'amount'         => number_format($price, 2, '.', ''),
//                     'balance_after'  => number_format($balanceAfter, 2, '.', ''),
//                     'type'           => 'debit',
//                     'request_id'     => $response->request_id,
//                     'pack_type'      => $plan,
//                     'notes'          => 'Communication charge for ' . strtoupper($channel) . ' - ' . $response->status,
//                     'created'        => time()
//                 ]);
//             }

//             $this->db->set([
//                 'is_captured'  => 1,
//                 'message_uuid' => $messageUuid,
//                 'bundled_paid' => $bundledPaidFlag,
//                 'sent_at' => isset($input['sent_at']) 
//                      ? date("Y-m-d H:i:s", $input['sent_at']) 
//                      : date("Y-m-d H:i:s"),

//             ])->where('request_id', $requestId)
//               ->update('tbl_notification_responses');
//         }
//     }

    
//     $this->db->trans_complete();

//     if ($this->db->trans_status() === FALSE) {
//         return $this->output
//             ->set_status_header(500)
//             ->set_output(json_encode(['error' => 'Database transaction failed']));
//     }

//     return $this->output
//         ->set_status_header(200)
//         ->set_output(json_encode(['message' => 'Status captured successfully']));
// }


public function msg91Webhookwhatsappoutbound_post()
{
    $input = json_decode(file_get_contents("php://input"), true);

    if (empty($input['request_id']) || empty($input['status'])) {
        return $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['error' => 'Invalid payload']));
    }

    $requestId   = $input['request_id'];
    $status      = strtolower($input['status']);
    $messageUuid = $input['message_uuid'] ?? null;

    $this->db->trans_start();

    
    $response = $this->db->query(
        "SELECT * FROM tbl_notification_responses WHERE request_id = ? FOR UPDATE",
        [$requestId]
    )->row();

    if (!$response) {
        $this->db->trans_complete();
        return $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['error' => 'Notification response not found']));
    }

    
    if ($status === 'failed') {
        $this->db->set(['delivery_status' => 'failed'])
            ->where('request_id', $requestId)
            ->update('tbl_notification_responses');

        $this->db->trans_complete();
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode(['message' => 'Failed status updated']));
    }

    
    $updateData = [
        'message_uuid' => $messageUuid
    ];

    
    if (isset($input['sent_at']) || $status === 'sent') {
        $updateData['sent_at'] = isset($input['sent_at'])
            ? date("Y-m-d H:i:s", $input['sent_at'])
            : date("Y-m-d H:i:s");
    }
    if (isset($input['delivered_at']) || $status === 'delivered') {
        $updateData['delivered_at'] = isset($input['delivered_at'])
            ? date("Y-m-d H:i:s", $input['delivered_at'])
            : date("Y-m-d H:i:s");
        $updateData['delivery_status'] = 'delivered';
    }
    if (isset($input['read_at']) || $status === 'read') {
        $updateData['read_at'] = isset($input['read_at'])
            ? date("Y-m-d H:i:s", $input['read_at'])
            : date("Y-m-d H:i:s");
    }

    
    if (in_array($status, ['sent', 'delivered', 'read']) && (int)$response->is_captured !== 1) {
        
        $user = $this->db->query(
            "SELECT * FROM tbl_users WHERE id = ? FOR UPDATE",
            [$response->user_id]
        )->row();

        if (!$user) {
            $this->db->trans_complete();
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode(['error' => 'User not found']));
        }

        $plan = strtolower($user->communication_plan ?? '');
        $channel = $response->channel_name;
        $price = 0;
        $bundledPaidFlag = 0;
        $orderSource = '';

        
        if (!empty($response->order_id)) {
            $order = $this->db->get_where('tbl_orders', ['id' => $response->order_id])->row();
            $orderSource = strtolower($order->order_source ?? '');
        } elseif (!empty($response->shipment_id)) {
            $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
            if ($shipment && !empty($shipment->order_id)) {
                $order = $this->db->get_where('tbl_orders', ['id' => $shipment->order_id])->row();
                $orderSource = strtolower($order->order_source ?? '');
            }
        }

        
        if ($plan === 'individual') {
            $alreadyBundledPaid = $this->db->where([
                'order_id'      => $response->order_id,
                'channel_name'  => $channel,
                'bundled_paid'  => 1,
                'is_captured'   => 1
            ])->get('tbl_notification_responses')->num_rows() > 0;

            if ($alreadyBundledPaid) {
                $price = 0;
                $bundledPaidFlag = 1;
            } else {
                $priceRow = $this->db
                    ->where('user_id', $response->user_id)
                    ->where('status', strtolower($response->status))
                    ->get('tbl_user_individual_price')
                    ->row();

                if (!$priceRow) {
                    $priceRow = $this->db
                        ->get_where('tbl_communication_individual_price', ['status' => strtolower($response->status)])
                        ->row();
                }
                $basePrice = $priceRow ? (float)$priceRow->$channel : 0;
                $price = round($basePrice * 1.18, 2); // GST
            }
        } elseif ($plan === 'bundled') {
            $specific = array_filter([
                $user->communication_specific1 ?? null,
                $user->communication_specific2 ?? null,
                $user->communication_specific3 ?? null,
                $user->communication_specific4 ?? null
            ]);

            if (!empty($specific) && in_array($channel, $specific)) {
                $orderIdToCheck = $response->order_id;

                if (empty($orderIdToCheck) && !empty($response->shipment_id)) {
                    $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
                    $orderIdToCheck = $shipment ? $shipment->order_id : null;
                }

                if (!empty($orderIdToCheck)) {
                    $initialStatuses = ['new', 'confirmation acknowledgement', 'pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception'];

                    $bundleRow = $this->db
                        ->where('user_id', $response->user_id)
                        ->get('tbl_user_bundeled_price')
                        ->row();

                    if (!$bundleRow) {
                        $bundleRow = $this->db
                            ->get('tbl_communication_bundeled_price')
                            ->row();
                    }
                    $basePrice = $bundleRow && isset($bundleRow->$channel) ? (float)$bundleRow->$channel : 0;

                    $alreadyCaptured = $this->db
                        ->where('order_id', $orderIdToCheck)
                        ->where('channel_name', $channel)
                        ->where_in('status', $initialStatuses)
                        ->where('bundled_paid', 1)
                        ->where('is_captured', 1)
                        ->limit(1)
                        ->get('tbl_notification_responses')
                        ->num_rows() > 0;

                    $price = $alreadyCaptured ? 0 : round($basePrice * 1.18, 2);
                    $bundledPaidFlag = 1;
                }
            }
        }

        
        if (($user->wallet_balance - $price) < $user->wallet_limit) {
            $this->db->trans_complete();
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'Insufficient balance']));
        }

        
        if ($price > 0) {
            $balanceBefore = $user->wallet_balance;
            $balanceAfter = $balanceBefore - $price;

            $this->db->set('wallet_balance', "wallet_balance - $price", false)
                ->where('id', $user->id)
                ->update('tbl_users');

            $orderNumber = '';
            if (!empty($response->order_id)) {
                $order = $this->db->get_where('tbl_orders', ['id' => $response->order_id])->row();
                if ($order) {
                    $orderNumber = !empty($order->api_order_id) ? $order->api_order_id : $order->order_no;
                }
            } elseif (!empty($response->shipment_id)) {
                $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
                if ($shipment && !empty($shipment->order_id)) {
                    $order = $this->db->get_where('tbl_orders', ['id' => $shipment->order_id])->row();
                    if ($order) {
                        $orderNumber = !empty($order->api_order_id) ? $order->api_order_id : $order->order_no;
                    }
                }
            }

            $this->db->insert('tbl_communication_wallet_history', [
                'user_id'        => $user->id,
                'txn_for'        => 'whatsapp',
                'txn_ref'        => $response->status,
                'ref_id'         => $response->order_id,
                'balance_before' => number_format($balanceBefore, 2, '.', ''),
                'amount'         => number_format($price, 2, '.', ''),
                'balance_after'  => number_format($balanceAfter, 2, '.', ''),
                'type'           => 'debit',
                'request_id'     => $response->request_id,
                'pack_type'      => $plan,
                'notes'          => 'Communication charge for ' . strtoupper($channel) . ' - ' . $response->status,
                'created'        => time()
            ]);
        }

        $updateData['is_captured']  = 1;
        $updateData['bundled_paid'] = $bundledPaidFlag;
    }

    
    if (!empty($updateData)) {
        $this->db->set($updateData)
            ->where('request_id', $requestId)
            ->update('tbl_notification_responses');
    }

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        return $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['error' => 'Database transaction failed']));
    }

    return $this->output
        ->set_status_header(200)
        ->set_output(json_encode(['message' => 'Status captured successfully']));
}





// public function msg91SmsWebhook_post()
// {
//     $input = json_decode(file_get_contents("php://input"), true);

//     $requestId = $input['requestId'] ?? null;
//     $status = $input['status'] ?? null;
//     $desc = strtolower($input['desc'] ?? '');
//     $uuid = $input['UUID'] ?? null;

//     if (!$requestId || !$status || !$desc) {
//         return $this->output
//             ->set_status_header(400)
//             ->set_output(json_encode(['error' => 'Invalid payload']));
//     }

//     if ($status != '1' || $desc !== 'delivered') {
//         return $this->output
//             ->set_status_header(200)
//             ->set_output(json_encode(['message' => 'Status not handled']));
//     }

//     $response = $this->db->get_where('tbl_notification_responses', [
//         'request_id' => $requestId
//     ])->row();

//     if (!$response) {
//         return $this->output
//             ->set_status_header(404)
//             ->set_output(json_encode(['error' => 'Notification response not found']));
//     }

//     if ((int)$response->is_captured === 1) {
//         return $this->output
//             ->set_status_header(200)
//             ->set_output(json_encode(['message' => 'Already captured']));
//     }

//     $user = $this->db->get_where('tbl_users', ['id' => $response->user_id])->row();
//     if (!$user) {
//         return $this->output
//             ->set_status_header(404)
//             ->set_output(json_encode(['error' => 'User not found']));
//     }

//     $plan = strtolower($user->communication_plan ?? '');
//     $channel = $response->channel_name;
//     $price = 0;
//     $bundledPaidFlag = 0;

//     $orderSource = '';
//     if (!empty($response->order_id)) {
//         $order = $this->db->get_where('tbl_orders', ['id' => $response->order_id])->row();
//         $orderSource = strtolower($order->order_source ?? '');
//     } elseif (!empty($response->shipment_id)) {
//         $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
//         if ($shipment && !empty($shipment->order_id)) {
//             $order = $this->db->get_where('tbl_orders', ['id' => $shipment->order_id])->row();
//             $orderSource = strtolower($order->order_source ?? '');
//         }
//     }

//     if ($plan === 'individual') {
//         // $priceRow = $this->db->get_where('tbl_communication_individual_price', [
//         //     'status' => strtolower($response->status)
//         // ])->row();
//         // $basePrice = $priceRow ? (float)$priceRow->$channel : 0;
//         // $price = round($basePrice * 1.18, 2); // Add 18% GST
//         $alreadyBundledPaid = $this->db->where([
//         'order_id'      => $response->order_id,
//         'channel_name'  => $channel,
//         'bundled_paid'  => 1,
//         'is_captured'   => 1
//     ])->get('tbl_notification_responses')->num_rows() > 0;
//     if ($alreadyBundledPaid) {
//         $price = 0;
//         $bundledPaidFlag = 1;
//     } else {
//         $priceRow = $this->db->get_where('tbl_communication_individual_price', [
//             'status' => strtolower($response->status)
//         ])->row();
//         $basePrice = $priceRow ? (float)$priceRow->$channel : 0;
//         $price = round($basePrice * 1.18, 2); // GST
//     }
//     } elseif ($plan === 'bundled') {
//         $specific = array_filter([
//             $user->communication_specific1 ?? null,
//             $user->communication_specific2 ?? null,
//             $user->communication_specific3 ?? null,
//             $user->communication_specific4 ?? null
//         ]);

//         if (!empty($specific) && in_array($channel, $specific)) {
//             $orderIdToCheck = $response->order_id;

//             if (empty($orderIdToCheck) && !empty($response->shipment_id)) {
//                 $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
//                 $orderIdToCheck = $shipment ? $shipment->order_id : null;
//             }

//             if (!empty($orderIdToCheck)) {
//                 $statusLower = strtolower($response->status);
//                 $initialStatuses = ['pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception']; // Allowed first-charge statuses

//                 if (in_array($statusLower, $initialStatuses)) {
//                     // Initial status → charge if not already bundled paid
//                     $alreadyCaptured = $this->db
//                         ->where('order_id', $orderIdToCheck)
//                         ->where('channel_name', $channel)
//                         ->where_in('status', $initialStatuses)
//                         ->where('bundled_paid', 1)
//                         ->where('is_captured', 1)
//                         ->get('tbl_notification_responses')
//                         ->num_rows();

//                     if ($alreadyCaptured > 0) {
//                         $price = 0;
//                         $bundledPaidFlag = 1;
//                     } else {
//                         $bundleRow = $this->db->get('tbl_communication_bundeled_price')->row();
//                         $basePrice = $bundleRow && isset($bundleRow->$channel) ? (float)$bundleRow->$channel : 0;
//                         $price = round($basePrice * 1.18, 2);
//                         $bundledPaidFlag = 1;
//                     }
//                 } else {
//                     // Later statuses → only charge if no bundled payment done yet
//                     $alreadyCaptured = $this->db
//                         ->where('order_id', $orderIdToCheck)
//                         ->where('channel_name', $channel)
//                         ->where_in('status', $initialStatuses)
//                         ->where('bundled_paid', 1)
//                         ->where('is_captured', 1)
//                         ->get('tbl_notification_responses')
//                         ->num_rows();

//                     if ($alreadyCaptured > 0) {
//                         $price = 0;
//                         $bundledPaidFlag = 1;
//                     } else {
//                         $bundleRow = $this->db->get('tbl_communication_bundeled_price')->row();
//                         $basePrice = $bundleRow && isset($bundleRow->$channel) ? (float)$bundleRow->$channel : 0;
//                         $price = round($basePrice * 1.18, 2);
//                         $bundledPaidFlag = 1;
//                     }
//                 }

//             }
//         }
//     }

//     // Start transaction
//     $this->db->trans_start();

//     if ($price > 0) {
//         if ($user->wallet_balance < $price) {
//             $this->db->trans_complete();
//             return $this->output
//                 ->set_status_header(400)
//                 ->set_output(json_encode(['error' => 'Insufficient wallet balance']));
//         }

//         $balanceBefore = $user->wallet_balance;
//         $balanceAfter = $balanceBefore - $price;

//         $this->db->set('wallet_balance', "wallet_balance - $price", false)
//             ->where('id', $user->id)
//             ->update('tbl_users');

//         $orderNumber = '';
//         if (!empty($response->order_id)) {
//             $order = $this->db->get_where('tbl_orders', ['id' => $response->order_id])->row();
//             if ($order) {
//                 $orderNumber = !empty($order->api_order_id) ? $order->api_order_id : $order->order_no;
//             }
//         } elseif (!empty($response->shipment_id)) {
//             $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
//             if ($shipment && !empty($shipment->order_id)) {
//                 $order = $this->db->get_where('tbl_orders', ['id' => $shipment->order_id])->row();
//                 if ($order) {
//                     $orderNumber = !empty($order->api_order_id) ? $order->api_order_id : $order->order_no;
//                 }
//             }
//         }

//         $this->db->insert('tbl_communication_wallet_history', [
//             'user_id' => $user->id,
//             'txn_for' => 'sms',
//             'txn_ref' => $response->status,
//             'ref_id' => $response->order_id,
//             'balance_before' => number_format($balanceBefore, 2, '.', ''),
//             'amount' => number_format($price, 2, '.', ''),
//             'balance_after' => number_format($balanceAfter, 2, '.', ''),
//             'type' => 'debit',
//             'request_id' => $response->request_id,
//             'pack_type' => $plan,
//             'notes' => 'Communication charge for ' . strtoupper($channel) . ' - ' . $response->status,
//             'created' => time()
//         ]);
//     }

//     $this->db->set([
//         'is_captured' => 1,
//         'message_uuid' => $uuid,
//         'bundled_paid' => $bundledPaidFlag
//     ])->where('request_id', $requestId)
//       ->update('tbl_notification_responses');

//     $this->db->trans_complete();

//     if ($this->db->trans_status() === FALSE) {
//         return $this->output
//             ->set_status_header(500)
//             ->set_output(json_encode(['error' => 'Database transaction failed']));
//     }

//     return $this->output
//         ->set_status_header(200)
//         ->set_output(json_encode(['message' => 'SMS delivered, balance handled']));
// }


// public function msg91SmsWebhook_post()
// {
//     $input = json_decode(file_get_contents("php://input"), true);

//     $requestId = $input['requestId'] ?? null;
//     $status = $input['status'] ?? null;
//     $desc = strtolower($input['desc'] ?? '');
//     $uuid = $input['UUID'] ?? null;
//     $date = $input['date'] ?? null;

//     if (!$requestId || !$status || !$desc) {
//         return $this->output
//             ->set_status_header(400)
//             ->set_output(json_encode(['error' => 'Invalid payload']));
//     }

//     if ($status != '1' || $desc !== 'delivered') {
//         return $this->output
//             ->set_status_header(200)
//             ->set_output(json_encode(['message' => 'Status not handled']));
//     }

//     // Start transaction early for row locking
//     $this->db->trans_start();

//     // Lock the notification response row
//     $response = $this->db
//         ->query("SELECT * FROM tbl_notification_responses WHERE request_id = ? FOR UPDATE", [$requestId])
//         ->row();

//     if (!$response) {
//         $this->db->trans_complete();
//         return $this->output
//             ->set_status_header(404)
//             ->set_output(json_encode(['error' => 'Notification response not found']));
//     }

//     if ((int)$response->is_captured === 1) {
//         $this->db->trans_complete();
//         return $this->output
//             ->set_status_header(200)
//             ->set_output(json_encode(['message' => 'Already captured']));
//     }

//     $user = $this->db
//         ->query("SELECT * FROM tbl_users WHERE id = ? FOR UPDATE", [$response->user_id])
//         ->row();

//     if (!$user) {
//         $this->db->trans_complete();
//         return $this->output
//             ->set_status_header(404)
//             ->set_output(json_encode(['error' => 'User not found']));
//     }

//     $plan = strtolower($user->communication_plan ?? '');
//     $channel = $response->channel_name;
//     $price = 0;
//     $bundledPaidFlag = 0;

//     $orderSource = '';
//     if (!empty($response->order_id)) {
//         $order = $this->db->get_where('tbl_orders', ['id' => $response->order_id])->row();
//         $orderSource = strtolower($order->order_source ?? '');
//     } elseif (!empty($response->shipment_id)) {
//         $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
//         if ($shipment && !empty($shipment->order_id)) {
//             $order = $this->db->get_where('tbl_orders', ['id' => $shipment->order_id])->row();
//             $orderSource = strtolower($order->order_source ?? '');
//         }
//     }

//     if ($plan === 'individual') {
//         $alreadyBundledPaid = $this->db->where([
//             'order_id'      => $response->order_id,
//             'channel_name'  => $channel,
//             'bundled_paid'  => 1,
//             'is_captured'   => 1
//         ])->get('tbl_notification_responses')->num_rows() > 0;

//         if ($alreadyBundledPaid) {
//             $price = 0;
//             $bundledPaidFlag = 1;
//         } else {
//             // $priceRow = $this->db->get_where('tbl_communication_individual_price', [
//             //     'status' => strtolower($response->status)
//             // ])->row();
//             $priceRow = $this->db
//                         ->where('user_id', $response->user_id)
//                         ->where('status', strtolower($response->status))
//                         ->get('tbl_user_individual_price')
//                         ->row();

//             if (!$priceRow) {
//                         $priceRow = $this->db
//                             ->get_where('tbl_communication_individual_price', ['status' => strtolower($response->status)])
//                             ->row();
//                     }
//             $basePrice = $priceRow ? (float)$priceRow->$channel : 0;
//             $price = round($basePrice * 1.18, 2);
//         }
//     } elseif ($plan === 'bundled') {
//         $specific = array_filter([
//             $user->communication_specific1 ?? null,
//             $user->communication_specific2 ?? null,
//             $user->communication_specific3 ?? null,
//             $user->communication_specific4 ?? null
//         ]);

//         if (!empty($specific) && in_array($channel, $specific)) {
//             $orderIdToCheck = $response->order_id;

//             if (empty($orderIdToCheck) && !empty($response->shipment_id)) {
//                 $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
//                 $orderIdToCheck = $shipment ? $shipment->order_id : null;
//             }

//             if (!empty($orderIdToCheck)) {
//                 $statusLower = strtolower($response->status);
//                 $initialStatuses = ['pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception'];

//                 $alreadyCaptured = $this->db
//                     ->where('order_id', $orderIdToCheck)
//                     ->where('channel_name', $channel)
//                     ->where_in('status', $initialStatuses)
//                     ->where('bundled_paid', 1)
//                     ->where('is_captured', 1)
//                     ->get('tbl_notification_responses')
//                     ->num_rows();

//                 if ($alreadyCaptured > 0) {
//                     $price = 0;
//                     $bundledPaidFlag = 1;
//                 } else {
//                     // $bundleRow = $this->db->get('tbl_communication_bundeled_price')->row();
//                     $bundleRow = $this->db
//                             ->where('user_id', $response->user_id)
//                             ->get('tbl_user_bundeled_price')
//                             ->row();

//                         if (!$bundleRow) {
//                             $bundleRow = $this->db
//                                 ->get('tbl_communication_bundeled_price')
//                                 ->row();
//                         }
//                     $basePrice = $bundleRow && isset($bundleRow->$channel) ? (float)$bundleRow->$channel : 0;
//                     $price = round($basePrice * 1.18, 2);
//                     $bundledPaidFlag = 1;
//                 }
//             }
//         }
//     }

//     if ($price > 0) {
//         if (($user->wallet_balance - $price) < $user->wallet_limit) {
//             $this->db->trans_complete();
//             return $this->output
//                 ->set_status_header(400)
//                 ->set_output(json_encode(['error' => 'Insufficient wallet balance']));
//         }

//         $balanceBefore = $user->wallet_balance;
//         $balanceAfter = $balanceBefore - $price;

//         $this->db->set('wallet_balance', "wallet_balance - $price", false)
//             ->where('id', $user->id)
//             ->update('tbl_users');

//         $orderNumber = '';
//         if (!empty($response->order_id)) {
//             $order = $this->db->get_where('tbl_orders', ['id' => $response->order_id])->row();
//             if ($order) {
//                 $orderNumber = !empty($order->api_order_id) ? $order->api_order_id : $order->order_no;
//             }
//         } elseif (!empty($response->shipment_id)) {
//             $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
//             if ($shipment && !empty($shipment->order_id)) {
//                 $order = $this->db->get_where('tbl_orders', ['id' => $shipment->order_id])->row();
//                 if ($order) {
//                     $orderNumber = !empty($order->api_order_id) ? $order->api_order_id : $order->order_no;
//                 }
//             }
//         }

//         $this->db->insert('tbl_communication_wallet_history', [
//             'user_id' => $user->id,
//             'txn_for' => 'sms',
//             'txn_ref' => $response->status,
//             'ref_id' => $response->order_id,
//             'balance_before' => number_format($balanceBefore, 2, '.', ''),
//             'amount' => number_format($price, 2, '.', ''),
//             'balance_after' => number_format($balanceAfter, 2, '.', ''),
//             'type' => 'debit',
//             'request_id' => $response->request_id,
//             'pack_type' => $plan,
//             'notes' => 'Communication charge for ' . strtoupper($channel) . ' - ' . $response->status,
//             'created' => time()
//         ]);
//     }

//     $this->db->set([
//         'is_captured' => 1,
//         'message_uuid' => $uuid,
//         'bundled_paid' => $bundledPaidFlag,
//         'delivered_at' => $date,
//         'delivery_status' => 'delivered'
//     ])->where('request_id', $requestId)
//       ->update('tbl_notification_responses');

//     $this->db->trans_complete();

//     if ($this->db->trans_status() === FALSE) {
//         return $this->output
//             ->set_status_header(500)
//             ->set_output(json_encode(['error' => 'Database transaction failed']));
//     }

//     return $this->output
//         ->set_status_header(200)
//         ->set_output(json_encode(['message' => 'SMS delivered, balance handled']));
// }

public function msg91SmsWebhook_post()
{
    $input = json_decode(file_get_contents("php://input"), true);

    $requestId = $input['requestId'] ?? null;
    $status = $input['status'] ?? null;
    $desc = strtolower($input['desc'] ?? '');
    $uuid = $input['UUID'] ?? null;
    $date = $input['date'] ?? null;
    $credit = isset($input['credit']) ? (int)$input['credit'] : 1;

    if (!$requestId || !$status || !$desc) {
        return $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['error' => 'Invalid payload']));
    }

    if (!in_array($desc, ['delivered', 'failed'])) {
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode(['message' => 'Status not handled']));
    }

    // Start transaction early for row locking
    $this->db->trans_start();

    // Lock the notification response row
    $response = $this->db
        ->query("SELECT * FROM tbl_notification_responses WHERE request_id = ? FOR UPDATE", [$requestId])
        ->row();

    if (!$response) {
        $this->db->trans_complete();
        return $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['error' => 'Notification response not found']));
    }

    if ((int)$response->is_captured === 1) {
        $this->db->trans_complete();
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode(['message' => 'Already captured']));
    }

    $user = $this->db
        ->query("SELECT * FROM tbl_users WHERE id = ? FOR UPDATE", [$response->user_id])
        ->row();

    if (!$user) {
        $this->db->trans_complete();
        return $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['error' => 'User not found']));
    }

    $plan = strtolower($user->communication_plan ?? '');
    $channel = $response->channel_name;
    $price = 0;
    $bundledPaidFlag = 0;

    $orderSource = '';
    if (!empty($response->order_id)) {
        $order = $this->db->get_where('tbl_orders', ['id' => $response->order_id])->row();
        $orderSource = strtolower($order->order_source ?? '');
    } elseif (!empty($response->shipment_id)) {
        $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
        if ($shipment && !empty($shipment->order_id)) {
            $order = $this->db->get_where('tbl_orders', ['id' => $shipment->order_id])->row();
            $orderSource = strtolower($order->order_source ?? '');
        }
    }

    if ($plan === 'individual') {
        $alreadyBundledPaid = $this->db->where([
            'order_id'      => $response->order_id,
            'channel_name'  => $channel,
            'bundled_paid'  => 1,
            'is_captured'   => 1
        ])->get('tbl_notification_responses')->num_rows() > 0;

        if ($alreadyBundledPaid) {
            $price = 0;
            $bundledPaidFlag = 1;
        } else {
            // $priceRow = $this->db->get_where('tbl_communication_individual_price', [
            //     'status' => strtolower($response->status)
            // ])->row();
            $priceRow = $this->db
                        ->where('user_id', $response->user_id)
                        ->where('status', strtolower($response->status))
                        ->get('tbl_user_individual_price')
                        ->row();

            if (!$priceRow) {
                        $priceRow = $this->db
                            ->get_where('tbl_communication_individual_price', ['status' => strtolower($response->status)])
                            ->row();
                    }
            $amount = $priceRow ? (float)$priceRow->$channel : 0;
            $basePrice = $amount * $credit;
            $price = round($basePrice * 1.18, 2);
        }
    } elseif ($plan === 'bundled') {
        $specific = array_filter([
            $user->communication_specific1 ?? null,
            $user->communication_specific2 ?? null,
            $user->communication_specific3 ?? null,
            $user->communication_specific4 ?? null
        ]);

        if (!empty($specific) && in_array($channel, $specific)) {
            $orderIdToCheck = $response->order_id;

            if (empty($orderIdToCheck) && !empty($response->shipment_id)) {
                $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
                $orderIdToCheck = $shipment ? $shipment->order_id : null;
            }

            if (!empty($orderIdToCheck)) {
                $statusLower = strtolower($response->status);
                $initialStatuses = ['pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception'];

                $alreadyCaptured = $this->db
                    ->where('order_id', $orderIdToCheck)
                    ->where('channel_name', $channel)
                    ->where_in('status', $initialStatuses)
                    ->where('bundled_paid', 1)
                    ->where('is_captured', 1)
                    ->get('tbl_notification_responses')
                    ->num_rows();

                if ($alreadyCaptured > 0) {
                    $price = 0;
                    $bundledPaidFlag = 1;
                } else {
                    // $bundleRow = $this->db->get('tbl_communication_bundeled_price')->row();
                    $bundleRow = $this->db
                            ->where('user_id', $response->user_id)
                            ->get('tbl_user_bundeled_price')
                            ->row();

                        if (!$bundleRow) {
                            $bundleRow = $this->db
                                ->get('tbl_communication_bundeled_price')
                                ->row();
                        }
                    $basePrice = $bundleRow && isset($bundleRow->$channel) ? (float)$bundleRow->$channel : 0;
                    $price = round($basePrice * 1.18, 2);
                    $bundledPaidFlag = 1;
                }
            }
        }
    }

    if ($price > 0) {
        if (($user->wallet_balance - $price) < $user->wallet_limit) {
            $this->db->trans_complete();
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'Insufficient wallet balance']));
        }

        $balanceBefore = $user->wallet_balance;
        $balanceAfter = $balanceBefore - $price;

        $this->db->set('wallet_balance', "wallet_balance - $price", false)
            ->where('id', $user->id)
            ->update('tbl_users');

        $orderNumber = '';
        if (!empty($response->order_id)) {
            $order = $this->db->get_where('tbl_orders', ['id' => $response->order_id])->row();
            if ($order) {
                $orderNumber = !empty($order->api_order_id) ? $order->api_order_id : $order->order_no;
            }
        } elseif (!empty($response->shipment_id)) {
            $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $response->shipment_id])->row();
            if ($shipment && !empty($shipment->order_id)) {
                $order = $this->db->get_where('tbl_orders', ['id' => $shipment->order_id])->row();
                if ($order) {
                    $orderNumber = !empty($order->api_order_id) ? $order->api_order_id : $order->order_no;
                }
            }
        }

        $this->db->insert('tbl_communication_wallet_history', [
            'user_id' => $user->id,
            'txn_for' => 'sms',
            'txn_ref' => $response->status,
            'ref_id' => $response->order_id,
            'balance_before' => number_format($balanceBefore, 2, '.', ''),
            'amount' => number_format($price, 2, '.', ''),
            'balance_after' => number_format($balanceAfter, 2, '.', ''),
            'type' => 'debit',
            'request_id' => $response->request_id,
            'pack_type' => $plan,
            'notes' => 'Communication charge for ' . strtoupper($channel) . ' - ' . $response->status,
            'created' => time()
        ]);
    }

    $this->db->set([
        'is_captured' => 1,
        'message_uuid' => $uuid,
        'bundled_paid' => $bundledPaidFlag,
        'delivered_at' => $date,
        'delivery_status' => $desc,
        'sms_length' => $credit
    ])->where('request_id', $requestId)
      ->update('tbl_notification_responses');

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        return $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['error' => 'Database transaction failed']));
    }

    return $this->output
        ->set_status_header(200)
        ->set_output(json_encode(['message' => 'SMS delivered, balance handled']));
}




public function whatsappInboundWebhook_post()
{
    // 1. Get incoming payload
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // 2. Validate required fields
    if (
        empty($data['message_uuid']) ||
        empty($data['button']['payload']) ||
        empty($data['button']['text']) ||
        empty($data['template_name']) ||
        empty($data['sender'])
    ) {
        return $this->output->set_status_header(400)->set_output('Bad Request');
    }

    $messageUUID = $data['replied_message_id'];
    $buttonPayload = strtoupper(trim($data['button']['payload']));
    $buttonText = strtoupper(trim($data['button']['text']));

    // 3. Check button logic
    $tagToApply = '';
    if ($buttonPayload === 'YES' && $buttonText === 'YES') {
        $tagToApply = 'confirmed';
    } elseif ($buttonPayload === 'NO' && $buttonText === 'NO') {
        $tagToApply = 'not confirmed';
    } else {
        return $this->output->set_status_header(200)->set_output('No Action');
    }

    // 4. Find order_id using message_uuid from tbl_notification_responses
    $notification = $this->db->get_where('tbl_notification_responses', ['message_uuid' => $messageUUID])->row();

    if (!$notification || empty($notification->order_id)) {
        return $this->output->set_status_header(404)->set_output('Notification not found');
    }

    $orderId = $notification->order_id;

    // 5. Begin DB Transaction
    $this->db->trans_start();

    // 6. Update order with tag
    $this->db->where('id', $orderId);
    $this->db->update('tbl_orders', ['applied_tags' => $tagToApply]);

    if (empty($notification->response)) {
        $this->db->where('id', $notification->id);
        $this->db->update('tbl_notification_responses', ['response' => strtolower($buttonPayload)]);
    }

    // 7. Optional: Send confirmation notification inside transaction
    if ($tagToApply === 'confirmed') {
        $this->load->library('notification_lib');
        $this->notification_lib->sendNotification(null, 'confirmation acknowledgement', $orderId);
    }

    // 8. Commit or Rollback
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        log_message('error', 'Transaction failed while tagging order ID: ' . $orderId);
        return $this->output->set_status_header(500)->set_output('Transaction Failed');
    }

    return $this->output->set_status_header(200)->set_output('Tag handled');
}


// public function emailDeliveryWebhook_post()
// {
//     $json = file_get_contents('php://input');
//     $data = json_decode($json);

//     if (!isset($data->data->outbound_email->unique_id)) {
//         return $this->output->set_status_header(400)->set_output('Invalid payload');
//     }

//     $uniqueId = $data->data->outbound_email->unique_id;
//     $messageId = $data->data->outbound_email->message_id ?? null;
//     $eventTitle = strtolower($data->data->event->title ?? '');

//     if ($eventTitle !== 'delivered') {
//         return $this->output->set_status_header(200)->set_output('Not a delivery event');
//     }

//     $notification = $this->db
//         ->where('request_id', $uniqueId)
//         ->where('channel_name', 'email')
//         ->get('tbl_notification_responses')
//         ->row();

//     if (!$notification) {
//         return $this->output->set_status_header(404)->set_output('Request ID not found');
//     }

//     if ((int)$notification->is_captured === 1) {
//         return $this->output->set_status_header(200)->set_output('Already captured.');
//     }

//     $user = $this->db->get_where('tbl_users', ['id' => $notification->user_id])->row();
//     if (!$user) {
//         return $this->output->set_status_header(404)->set_output('User not found');
//     }

//     $channel = 'email';
//     $plan = strtolower($user->communication_plan ?? '');
//     $price = 0;
//     $bundledPaidFlag = 0;

//     $orderSource = '';
//     if (!empty($notification->order_id)) {
//         $order = $this->db->get_where('tbl_orders', ['id' => $notification->order_id])->row();
//         $orderSource = strtolower($order->order_source ?? '');
//     } elseif (!empty($notification->shipment_id)) {
//         $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $notification->shipment_id])->row();
//         if ($shipment && !empty($shipment->order_id)) {
//             $order = $this->db->get_where('tbl_orders', ['id' => $shipment->order_id])->row();
//             $orderSource = strtolower($order->order_source ?? '');
//         }
//     }

//     if ($plan === 'individual') {
//         // $priceRow = $this->db->get_where('tbl_communication_individual_price', [
//         //     'status' => strtolower($notification->status)
//         // ])->row();
//         // $basePrice = $priceRow ? (float)$priceRow->$channel : 0;
//         // $price = round($basePrice * 1.18, 2); // Add 18% GST
//         $alreadyBundledPaid = $this->db->where([
//         'order_id'      => $notification->order_id,
//         'channel_name'  => $channel,
//         'bundled_paid'  => 1,
//         'is_captured'   => 1
//     ])->get('tbl_notification_responses')->num_rows() > 0;
//     if ($alreadyBundledPaid) {
//         $price = 0;
//         $bundledPaidFlag = 1;
//     } else {
//         $priceRow = $this->db->get_where('tbl_communication_individual_price', [
//             'status' => strtolower($notification->status)
//         ])->row();
//         $basePrice = $priceRow ? (float)$priceRow->$channel : 0;
//         $price = round($basePrice * 1.18, 2); // GST
//     }

//     } elseif ($plan === 'bundled') {
//         $specifics = array_filter([
//             $user->communication_specific1 ?? null,
//             $user->communication_specific2 ?? null,
//             $user->communication_specific3 ?? null,
//             $user->communication_specific4 ?? null
//         ]);

//         if (in_array($channel, $specifics)) {
//             $orderIdToCheck = $notification->order_id;

//             if (empty($orderIdToCheck) && !empty($notification->shipment_id)) {
//                 $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $notification->shipment_id])->row();
//                 $orderIdToCheck = $shipment ? $shipment->order_id : null;
//             }

//             if (!empty($orderIdToCheck)) {
//                 $statusLower = strtolower($notification->status);
//                 $initialStatuses = ['pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception']; // Allowed first-charge statuses

//                 if (in_array($statusLower, $initialStatuses)) {
//                     // Initial status → charge if not already bundled paid
//                     $alreadyCaptured = $this->db
//                         ->where('order_id', $orderIdToCheck)
//                         ->where('channel_name', $channel)
//                         ->where_in('status', $initialStatuses)
//                         ->where('bundled_paid', 1)
//                         ->where('is_captured', 1)
//                         ->get('tbl_notification_responses')
//                         ->num_rows();

//                     if ($alreadyCaptured > 0) {
//                         $price = 0;
//                         $bundledPaidFlag = 1;
//                     } else {
//                         $bundleRow = $this->db->get('tbl_communication_bundeled_price')->row();
//                         $basePrice = $bundleRow && isset($bundleRow->$channel) ? (float)$bundleRow->$channel : 0;
//                         $price = round($basePrice * 1.18, 2);
//                         $bundledPaidFlag = 1;
//                     }
//                 } else {
//                     // Later statuses → only charge if no bundled payment done yet
//                     $alreadyCaptured = $this->db
//                         ->where('order_id', $orderIdToCheck)
//                         ->where('channel_name', $channel)
//                         ->where_in('status', $initialStatuses)
//                         ->where('bundled_paid', 1)
//                         ->where('is_captured', 1)
//                         ->get('tbl_notification_responses')
//                         ->num_rows();

//                     if ($alreadyCaptured > 0) {
//                         $price = 0;
//                         $bundledPaidFlag = 1;
//                     } else {
//                         $bundleRow = $this->db->get('tbl_communication_bundeled_price')->row();
//                         $basePrice = $bundleRow && isset($bundleRow->$channel) ? (float)$bundleRow->$channel : 0;
//                         $price = round($basePrice * 1.18, 2);
//                         $bundledPaidFlag = 1;
//                     }
//                 }
//             }
//         }
//     }

//     $balanceBefore = floatval($user->wallet_balance);

//     //  Start transaction
//     $this->db->trans_start();

//     if ($price > 0) {
//         if ($balanceBefore < $price) {
//             $this->db->trans_complete(); // Rollback
//             return $this->output->set_status_header(400)->set_output('Insufficient wallet balance');
//         }

//         $balanceAfter = $balanceBefore - $price;

//         $this->db->where('id', $user->id)->update('tbl_users', [
//             'wallet_balance' => $balanceAfter
//         ]);

//         $orderNumber = '';
//         if (!empty($notification->order_id)) {
//             $order = $this->db->get_where('tbl_orders', ['id' => $notification->order_id])->row();
//             if ($order) {
//                 $orderNumber = !empty($order->api_order_id) ? $order->api_order_id : $order->order_no;
//             }
//         } elseif (!empty($notification->shipment_id)) {
//             $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $notification->shipment_id])->row();
//             if ($shipment && !empty($shipment->order_id)) {
//                 $order = $this->db->get_where('tbl_orders', ['id' => $shipment->order_id])->row();
//                 if ($order) {
//                     $orderNumber = !empty($order->api_order_id) ? $order->api_order_id : $order->order_no;
//                 }
//             }
//         }

//         $this->db->insert('tbl_communication_wallet_history', [
//             'user_id' => $user->id,
//             'txn_for' => 'email',
//             'txn_ref' => $notification->status,
//             'ref_id' => $notification->order_id,
//             'balance_before' => number_format($balanceBefore, 2, '.', ''),
//             'amount' => number_format($price, 2, '.', ''),
//             'balance_after' => number_format($balanceAfter, 2, '.', ''),
//             'type' => 'debit',
//             'request_id' => $notification->request_id,
//             'pack_type' => $plan,
//             'notes' => 'Communication charge for EMAIL - ' . strtoupper($notification->status),
//             'created' => time()
//         ]);
//     }

//     $this->db->where('id', $notification->id)->update('tbl_notification_responses', [
//         'is_captured' => 1,
//         'message_uuid' => $messageId,
//         'bundled_paid' => $bundledPaidFlag
//     ]);

//     //  Commit transaction
//     $this->db->trans_complete();

//     if (!$this->db->trans_status()) {
//         return $this->output->set_status_header(500)->set_output('Transaction failed');
//     }

//     return $this->output->set_status_header(200)->set_output('Email status captured and processed');
// }

public function emailDeliveryWebhook_post()
{
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    if (!isset($data->data->outbound_email->unique_id)) {
        return $this->output->set_status_header(400)->set_output('Invalid payload');
    }

    $uniqueId = $data->data->outbound_email->unique_id;
    $messageId = $data->data->outbound_email->message_id ?? null;
    $eventTitle = strtolower($data->data->event->title ?? '');

    if ($eventTitle !== 'delivered') {
        return $this->output->set_status_header(200)->set_output('Not a delivery event');
    }

    // ✅ Start transaction early so locking applies immediately
    $this->db->trans_start();

    // ✅ Lock the notification row to prevent concurrent deductions
    $notification = $this->db->query("
        SELECT * 
        FROM tbl_notification_responses 
        WHERE request_id = ? 
          AND channel_name = 'email' 
        FOR UPDATE
    ", [$uniqueId])->row();

    if (!$notification) {
        $this->db->trans_complete();
        return $this->output->set_status_header(404)->set_output('Request ID not found');
    }

    if ((int)$notification->is_captured === 1) {
        $this->db->trans_complete();
        return $this->output->set_status_header(200)->set_output('Already captured.');
    }

    $user = $this->db->query("
        SELECT * FROM tbl_users WHERE id = ? FOR UPDATE
    ", [$notification->user_id])->row(); // ✅ Lock user row to prevent race on wallet balance

    if (!$user) {
        $this->db->trans_complete();
        return $this->output->set_status_header(404)->set_output('User not found');
    }

    $channel = 'email';
    $plan = strtolower($user->communication_plan ?? '');
    $price = 0;
    $bundledPaidFlag = 0;

    $orderSource = '';
    if (!empty($notification->order_id)) {
        $order = $this->db->get_where('tbl_orders', ['id' => $notification->order_id])->row();
        $orderSource = strtolower($order->order_source ?? '');
    } elseif (!empty($notification->shipment_id)) {
        $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $notification->shipment_id])->row();
        if ($shipment && !empty($shipment->order_id)) {
            $order = $this->db->get_where('tbl_orders', ['id' => $shipment->order_id])->row();
            $orderSource = strtolower($order->order_source ?? '');
        }
    }

    if ($plan === 'individual') {
        $alreadyBundledPaid = $this->db->where([
            'order_id'      => $notification->order_id,
            'channel_name'  => $channel,
            'bundled_paid'  => 1,
            'is_captured'   => 1
        ])->get('tbl_notification_responses')->num_rows() > 0;

        if ($alreadyBundledPaid) {
            $price = 0;
            $bundledPaidFlag = 1;
        } else {
            // $priceRow = $this->db->get_where('tbl_communication_individual_price', [
            //     'status' => strtolower($notification->status)
            // ])->row();
            $priceRow = $this->db
                        ->where('user_id', $notification->user_id)
                        ->where('status', strtolower($notification->status))
                        ->get('tbl_user_individual_price')
                        ->row();

                    if (!$priceRow) {
                        $priceRow = $this->db
                            ->get_where('tbl_communication_individual_price', ['status' => strtolower($notification->status)])
                            ->row();
                    }
            $basePrice = $priceRow ? (float)$priceRow->$channel : 0;
            $price = round($basePrice * 1.18, 2);
        }
    } elseif ($plan === 'bundled') {
        $specifics = array_filter([
            $user->communication_specific1 ?? null,
            $user->communication_specific2 ?? null,
            $user->communication_specific3 ?? null,
            $user->communication_specific4 ?? null
        ]);

        if (in_array($channel, $specifics)) {
            $orderIdToCheck = $notification->order_id;

            if (empty($orderIdToCheck) && !empty($notification->shipment_id)) {
                $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $notification->shipment_id])->row();
                $orderIdToCheck = $shipment ? $shipment->order_id : null;
            }

            if (!empty($orderIdToCheck)) {
                $statusLower = strtolower($notification->status);
                $initialStatuses = ['pending pickup', 'in transit', 'out for delivery', 'delivered', 'rto in transit', 'exception'];

                $alreadyCaptured = $this->db
                    ->where('order_id', $orderIdToCheck)
                    ->where('channel_name', $channel)
                    ->where_in('status', $initialStatuses)
                    ->where('bundled_paid', 1)
                    ->where('is_captured', 1)
                    ->get('tbl_notification_responses')
                    ->num_rows();

                if ($alreadyCaptured > 0) {
                    $price = 0;
                    $bundledPaidFlag = 1;
                } else {
                    // $bundleRow = $this->db->get('tbl_communication_bundeled_price')->row();
                    $bundleRow = $this->db
                            ->where('user_id', $notification->user_id)
                            ->get('tbl_user_bundeled_price')
                            ->row();

                        if (!$bundleRow) {
                            $bundleRow = $this->db
                                ->get('tbl_communication_bundeled_price')
                                ->row();
                        }
                    $basePrice = $bundleRow && isset($bundleRow->$channel) ? (float)$bundleRow->$channel : 0;
                    $price = round($basePrice * 1.18, 2);
                    $bundledPaidFlag = 1;
                }
            }
        }
    }

    $balanceBefore = floatval($user->wallet_balance);

    if ($price > 0) {
        if (($user->wallet_balance - $price) < $user->wallet_limit) {
            $this->db->trans_complete(); // rollback
            return $this->output->set_status_header(400)->set_output('Insufficient wallet balance');
        }

        $balanceAfter = $balanceBefore - $price;

        $this->db->where('id', $user->id)->update('tbl_users', [
            'wallet_balance' => $balanceAfter
        ]);

        $orderNumber = '';
        if (!empty($notification->order_id)) {
            $order = $this->db->get_where('tbl_orders', ['id' => $notification->order_id])->row();
            if ($order) {
                $orderNumber = !empty($order->api_order_id) ? $order->api_order_id : $order->order_no;
            }
        } elseif (!empty($notification->shipment_id)) {
            $shipment = $this->db->get_where('tbl_order_shipping', ['id' => $notification->shipment_id])->row();
            if ($shipment && !empty($shipment->order_id)) {
                $order = $this->db->get_where('tbl_orders', ['id' => $shipment->order_id])->row();
                if ($order) {
                    $orderNumber = !empty($order->api_order_id) ? $order->api_order_id : $order->order_no;
                }
            }
        }

        $this->db->insert('tbl_communication_wallet_history', [
            'user_id' => $user->id,
            'txn_for' => 'email',
            'txn_ref' => $notification->status,
            'ref_id' => $notification->order_id,
            'balance_before' => number_format($balanceBefore, 2, '.', ''),
            'amount' => number_format($price, 2, '.', ''),
            'balance_after' => number_format($balanceAfter, 2, '.', ''),
            'type' => 'debit',
            'request_id' => $notification->request_id,
            'pack_type' => $plan,
            'notes' => 'Communication charge for EMAIL - ' . strtoupper($notification->status),
            'created' => time()
        ]);
    }

    $this->db->where('id', $notification->id)->update('tbl_notification_responses', [
        'is_captured' => 1,
        'message_uuid' => $messageId,
        'bundled_paid' => $bundledPaidFlag
    ]);

    // ✅ Commit with the lock in place
    $this->db->trans_complete();

    if (!$this->db->trans_status()) {
        return $this->output->set_status_header(500)->set_output('Transaction failed');
    }

    return $this->output->set_status_header(200)->set_output('Email status captured and processed');
}







    
}
