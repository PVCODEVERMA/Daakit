<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';

class Exotelwebhook extends RestController
{
    public function __construct()
    {
        parent::__construct('rest_api');
        
    }


    public function exotelStatusWebhook_post()
{
   
     $input = $_POST;

    if (empty($input)) {
        return $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['error' => 'Empty webhook payload']));
    }

    $callSid = $input['CallSid'] ?? null;
    $status  = strtolower($input['Status'] ?? null);
    $dateUpdated = $input['DateUpdated'] ?? date('Y-m-d H:i:s');

    if (!$callSid || !$status) {
        return $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['error' => 'CallSid or Status missing']));
    }

    
    $this->db->trans_start();

   
    $response = $this->db
        ->query("SELECT * FROM tbl_notification_responses WHERE request_id = ? FOR UPDATE", [$callSid])
        ->row();

    if (!$response) {
        $this->db->trans_complete();
        return $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['error' => 'Notification response not found']));
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

   
    if ((int)$response->is_captured === 1) {
        $this->db->set('delivery_status', $status)
                 ->where('request_id', $callSid)
                 ->update('tbl_notification_responses');

        $this->db->trans_complete();
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode(['message' => 'Status updated, already captured']));
    }


    if ($status === 'completed') {
        $plan = strtolower($user->communication_plan ?? '');
        $channel = 'ivr';
        $price = 0;
        $bundledPaidFlag = 0;
        $credit = 1;
        $orderId = $response->order_id;

       
        if ($plan === 'individual') {
            $alreadyBundledPaid = $this->db->where([
                'order_id'      => $orderId,
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
                    $priceRow = $this->db->get_where('tbl_communication_individual_price', ['status' => strtolower($response->status)])->row();
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
                $alreadyCaptured = $this->db
                    ->where('order_id', $orderId)
                    ->where('channel_name', $channel)
                    ->where('bundled_paid', 1)
                    ->where('is_captured', 1)
                    ->get('tbl_notification_responses')
                    ->num_rows();

                if ($alreadyCaptured > 0) {
                    $price = 0;
                    $bundledPaidFlag = 1;
                } else {
                    $bundleRow = $this->db
                        ->where('user_id', $response->user_id)
                        ->get('tbl_user_bundeled_price')
                        ->row();
                    if (!$bundleRow) {
                        $bundleRow = $this->db->get('tbl_communication_bundeled_price')->row();
                    }

                    $amount = $bundleRow && isset($bundleRow->$channel) ? (float)$bundleRow->$channel : 0;
                    $basePrice = $amount * $credit;
                    $price = round($basePrice * 1.18, 2);
                    $bundledPaidFlag = 1;
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

            $this->db->insert('tbl_communication_wallet_history', [
                'user_id' => $user->id,
                'txn_for' => 'ivr',
                'txn_ref' => $status,
                'ref_id' => $orderId,
                'balance_before' => number_format($balanceBefore, 2, '.', ''),
                'amount' => number_format($price, 2, '.', ''),
                'balance_after' => number_format($balanceAfter, 2, '.', ''),
                'type' => 'debit',
                'request_id' => $callSid,
                'pack_type' => $plan,
                'notes' => 'IVR call charge - ' . strtoupper($status),
                'created' => time()
            ]);
        }

      
        $this->db->set([
            'is_captured' => 1,
            'bundled_paid' => $bundledPaidFlag,
            'delivered_at' => $dateUpdated,
            'delivery_status' => $status,
        ])->where('request_id', $callSid)
          ->update('tbl_notification_responses');
    } else {
       
        $this->db->set([
            'delivery_status' => $status,
            'delivered_at' => $dateUpdated
        ])->where('request_id', $callSid)
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
        ->set_output(json_encode(['message' => 'Exotel status webhook processed']));
}


    public function exotelWebhook_get()
    {
        
        $input = $this->input->post();

        if (empty($input)) {
            $input = $this->input->get();
        }

        if (empty($input)) {
            $input = json_decode(file_get_contents("php://input"), true);

            if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data') !== false) {
                $input = $this->_parseMultipart($raw, $_SERVER['CONTENT_TYPE']);
            }
        }

        if (empty($input)) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'Empty webhook payload']));
        }

        
        $callSid = $input['CallSid'] ?? null;
        $digits  = isset($input['digits']) ? trim($input['digits'], '"') : null;
        $status  = $input['Status'] ?? null;
        $date    = $input['DateUpdated'] ?? null;

        if (!$callSid) {
        
            log_message('error', 'Exotel webhook raw input: ' . print_r($input, true));

            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'CallSid missing']));
        }

        $this->db->trans_start();

        $responseRow = $this->db
            ->query("SELECT * FROM tbl_notification_responses WHERE request_id = ? FOR UPDATE", [$callSid])
            ->row();

        if (!$responseRow) {
            $this->db->trans_complete();
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode(['error' => 'Notification response not found']));
        }

        
        if (!empty($digits) && !empty($responseRow->order_id)) {
        
        $ivrStatus = 1; 
        $ivr_response = $digits;        
        $ivrCstatus = null;
        $request_data = json_encode($input, JSON_UNESCAPED_UNICODE);     

        if ($digits === "1") {
            $ivrCstatus = "confirmed"; 
        } elseif ($digits === "2") {
            $ivrCstatus = "cancelled";
        }

        
        $this->db->set([
                'ivr_calling_status' => $ivrStatus,
                'ivr_status'         => $ivrCstatus
            ])
            ->where('id', $responseRow->order_id)
            ->update('tbl_orders');

    
        $this->db->set([
                'response'    => $ivr_response,
                'request_data' => $request_data
            ])
            ->where('request_id', $callSid)
            ->update('tbl_notification_responses');
    }
    
        if (!empty($status)) {
            $updateData = [
                'delivery_status' => $status,
                'delivered_at'    => $date ?? date('Y-m-d H:i:s'),
            ];

            $this->db->set($updateData)
                    ->where('request_id', $callSid)
                    ->update('tbl_notification_responses');
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return $this->output
                ->set_status_header(500)
                ->set_output(json_encode(['error' => 'Database update failed']));
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'message' => 'Exotel webhook processed',
                'callSid' => $callSid,
                'digits'  => $digits,
                'status'  => $status,
                'date'    => $date
            ]));
    }



}