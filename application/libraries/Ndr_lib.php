<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ndr_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('ndr_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->ndr_model, $method)) {
            throw new Exception('Undefined method ndr_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->ndr_model, $method], $arguments);
    }

    function sendNdrSms($action_id = false)
    {
        if (!$action_id)
            return false;

        $action = $this->getByActionID($action_id);
        if (empty($action))
            return false;

        if ($action->action != 'ndr')
            return false;

        switch ($action->ndr_code) {
            case 'wrong_mobile':
            case 'wrong_address':
            case 'customer_cancelled':
            case 'reschedule':
            case 'unavailable':
            case 'restricted':
            case 'need_details':
                break;
            default:
                return false;
        }

        $ndr = $this->getByID($action->ndr_id);
        if (empty($ndr))
            return false;

        $this->CI->load->library('shipping_lib');
        $shipment = $this->CI->shipping_lib->getShipmentByID($ndr->shipment_id);

        if (empty($shipment))
            return false;

        $this->CI->load->library('apps/aftership_lib');
        $aftership_data = $this->CI->aftership_lib->getByUserID($shipment->user_id);

        if (!empty($aftership_data)) {
            if ($aftership_data->send_sms == '0')
                return false; //sms disabled
            if ($aftership_data->ndr_sms == '0')
                return false; //sms disabled
        }

        $this->CI->load->library('courier_lib');
        $courier = $this->CI->courier_lib->getByID($shipment->courier_id);

        $this->data['ndr'] = $ndr;
        $this->data['shipment'] = $shipment;
        $this->data['courier'] = $courier;

        $this->CI->load->library('sms');
        $sms = new sms();
        $sms->send_sms($shipment->shipping_phone, 'ndr', $this->data);

        $update = array(
            'ndr_sms' => '1'
        );

        $this->CI->load->library('shipping_sms_lib');
        $this->CI->shipping_sms_lib->updateStatusSMS($ndr->shipment_id, $update);
    }
    function sendNdrWhatsapp($action_id = false)
    {
        return false;
        if (!$action_id)
            return false;

        $action = $this->getByActionID($action_id);
        if (empty($action))
            return false;

        if ($action->action != 'ndr')
            return false;

        // switch ($action->ndr_code) {
        //     case 'wrong_mobile':
        //     case 'wrong_address':
        //     case 'customer_cancelled':
        //     case 'reschedule':
        //     case 'unavailable':
        //     case 'restricted':
        //     case 'need_details':
        //         break;
        //     default:
        //         return false;
        // }

        $ndr = $this->getByID($action->ndr_id);
        if (empty($ndr))
            return false;

        $this->CI->load->library('shipping_lib');
        $shipment = $this->CI->shipping_lib->getShipmentByID($ndr->shipment_id);
        if (empty($shipment))
            return false;

        $this->CI->load->library('apps/whatsapp_lib');
        $aftership_data = $this->CI->whatsapp_lib->getByUserID($shipment->user_id);
        if (empty($aftership_data)) {
            return false;
        }
        if ($aftership_data->send_sms == '0')
            return false; //sms disabled
        if ($aftership_data->ndr_sms == '0')
            return false; //sms disabled

        $this->CI->load->library('shipping_whatsapp_lib');
        $shipmentSMS =$this->CI->shipping_whatsapp_lib->getShipmentSMSByID($ndr->shipment_id);
    
        if(isset($shipmentSMS->ndr_sms) && $shipmentSMS->ndr_sms == 1)
            return false;
    
        $this->CI->load->library('courier_lib');
        $courier = $this->CI->courier_lib->getByID($shipment->courier_id);

        $this->data['ndr'] = $ndr;
        $this->data['shipment'] = $shipment;
        $this->data['courier'] = $courier;

        $this->CI->load->library('whatsapp');
        // $sms = new whatsapp();
        // $ship_data= array(
        //     'fullname'  =>  $shipment->shipping_fname.' '.$shipment->shipping_lname,
        //     'awb_number'    =>  $shipment->awb_number,
        //     'remark'        =>  $action->remarks,
        //     'action_id'     =>  $action->id,
        //     'shipping_id'   =>  $shipment->id,
        //     'ndr_code'      =>  $action->ndr_code,
        //     'user_id'       =>  $shipment->user_id
        // );
        // $sms->send_whatsapp($shipment->shipping_phone, $ship_data, 'ndr');

        // $update = array(
        //     'ndr_sms' => '1'
        // );
        // $this->CI->shipping_whatsapp_lib->updateStatusSMS($ndr->shipment_id, $update);
    }

    function manageNDRAgainstShipStatus($shipment_id = false, $event = false)
    {
        if (!$shipment_id || empty($event))
            return false;

        if ($event['ship_status'] != 'exception')  //current status is not exception 
            return false;

        $this->CI->load->library('shipping_lib');
        $shipment = $this->CI->shipping_lib->getByID($shipment_id);

        if (empty($shipment))
            return false;

        ////check if shipment is not exception and no existing record of ndr
        $ndr = $this->getByShippingID($shipment_id);



        $this->CI->load->library('orders_lib');
        $order = $this->CI->orders_lib->getByID($shipment->order_id);

        //if existing ndr then update else create new ndr
        if (empty($ndr)) {
            //create new ndr
            $save = array(
                'user_id' => $order->user_id,
                'order_id' => $shipment->order_id,
                'shipment_id' => $shipment_id,
                'total_attempts' => '1',
                'last_action_by' => 'courier',
                'last_action' => 'ndr',
                'latest_remarks' => !empty($event['message']) ? $event['message'] : '',
                'last_event' => time(),
            );

            $ndr_id = $this->insert($save);
        } else {
            $ndr_id = $ndr->id;
        }

        //$this->CI->load->library('shipping_whatsapp_lib');
        $update = array(
            'ndr_sms' => '1'
        );
        // $this->CI->load->library('apps/whatsapp_lib');
        // $this->CI->whatsapp_lib->updateStatusSMS($shipment_id, $update, 'shipment');
        $this->CI->load->library('shipping_sms_lib');
        $this->CI->shipping_sms_lib->updateStatusSMS($shipment_id, $update);
        $this->updateNDRCourierAction($ndr_id, $event);
    }

    function updateNDRCourierAction($ndr_id = false, $event = false)
    {
        if (!$ndr_id || empty($event))
            return false;

        //get last action by courier
        $last_action = $this->ndrCourierLastAction($ndr_id);
        $save = array(
            'ndr_id' => $ndr_id,
            'action' => 'ndr',
            'remarks' => $event['message'],
            'ndr_code' => $this->filterExceptionMessage($event['message']),
            'attempt' => 1,
            'source' => 'courier',
            'event_time' => $event['event_time'],
        );

        $update_ndr = $action_id = false;
        if (empty($last_action)) {
            $action_id = $this->insert_action($save);
            $update_ndr = array(
                'last_action_id' => $action_id
            );
        } elseif ($event['event_time'] > $last_action->event_time) {
            //another exception received
            $save['attempt'] = $last_action->attempt + 1;
            $action_id = $this->insert_action($save);

            $update_ndr = array(
                'total_attempts' => $save['attempt'],
                'last_action_by' => 'courier',
                'last_action' => 'ndr',
                'latest_remarks' => $save['remarks'],
                'last_event' => time(),
                'last_action_id' => $action_id
            );
        }
        
        if ($action_id) {
            $this->update($ndr_id, $update_ndr);

            do_action('ndr.new_action', $action_id);
            do_action('ndr.whatsapp', $action_id);
        }

        return true;
    }

    function filterExceptionMessage($message = false)
    {
        if (!$message)
            return false;

        $message = strtolower($message);

        if (strpos($message, 'address') !== false) {
            $message = 'wrong adddress';
        }
        if (strpos($message, 'mobile not reachable') !== false) {
            $message = 'wrong mobile';
        }
        if (strpos($message, 'refused') !== false) {
            $message = 'customer cancelled';
        }
        if (strpos($message, 'reschedule') !== false) {
            $message = 'reschedule';
        }
        if (strpos($message, 'another date') !== false) {
            $message = 'reschedule';
        }
        if (strpos($message, 'future') !== false) {
            $message = 'reschedule';
        }
        if (strpos($message, 'next business') !== false) {
            $message = 'reschedule';
        }
        if (strpos($message, 'next working day') !== false) {
            $message = 'reschedule';
        }
        if (strpos($message, 'available') !== false) {
            $message = 'unavailable';
        }
        if (strpos($message, 'closed') !== false) {
            $message = 'closed';
        }
        if (strpos($message, 'open delivery') !== false) {
            $message = 'open delivery';
        }
        if (strpos($message, 'restricted') !== false) {
            $message = 'restricted';
        }
        if (strpos($message, 'held') !== false) {
            $message = 'held';
        }
        if (strpos($message, 'seized') !== false) {
            $message = 'held';
        }
        if (strpos($message, 'maximum') !== false) {
            $message = 'maximum';
        }
        if (strpos($message, 'self') !== false) {
            $message = 'self';
        }
        if (strpos($message, 'not ready') !== false) {
            $message = 'amount not ready';
        }
        if (strpos($message, 'contact customer service') !== false) {
            $message = 'contact courier';
        }
        if (strpos($message, 'need') !== false) {
            $message = 'need details';
        }

        if (strpos($message, 'pincode') !== false) {
            $message = 'wrong pincode';
        }

        if (strpos($message, 'out of delivery area') !== false) {
            $message = 'oda';
        }

        $status = '';
        switch ($message) {
            case 'wrong adddress':
                $status = 'wrong_address';
                break;
            case 'wrong mobile':
            case 'customer not contactable':
                $status = 'wrong_mobile';
                break;
            case 'customer cancelled':
            case 'cancelled by customer':
                $status = 'customer_cancelled';
                break;
            case 'reschedule':
                $status = 'reschedule';
                break;

            case 'unavailable':
            case 'consignee out of station':
            case 'customer out of station':
                $status = 'unavailable';
                break;
            case 'closed':
                $status = 'closed';
                break;
            case 'open delivery':
                $status = 'open_delivery';
                break;
            case 'restricted':
            case 'entry not permitted':
                $status = 'restricted';
                break;
            case 'held':
                $status = 'held';
                break;
            case 'maximum':
                $status = 'maximum_attempt';
                break;
            case 'self':
                $status = 'self_collect';
                break;
            case 'amount not ready':
            case 'payment mode / amt dispute':
                $status = 'amount_not_ready';
                break;
            case 'delivery area':
                $status = 'oda';
                break;
            case 'contact courier':
                $status = 'contact_courier';
                break;
            case 'need details':
                $status = 'need_details';
                break;
            case 'wrong pincode':
                $status = 'wrong_pincode';
                break;
            case 'oda':
                $status = 'oda';
                break;
            default:
        }

        return $status;
    }

    function AddNDRAction($action = false, $user_id = false)
    {
        
        if (empty($action['ndr_id']) || empty($action['source'])) {
            $this->error = 'Invalid Request';
            return false;
        }
        $ndr = $this->getByID($action['ndr_id']);
        if (empty($ndr)) {
            $this->error = 'NDR not found';
            return false;
        }
        if ($user_id && $ndr->user_id != $user_id) {
            $this->error = 'NDR not found';
            return false;
        }
        if ($ndr->last_action_by != 'courier') {
            $this->error = 'No Courier Exception Available';
            return false;
        }
        $save = array(
            'ndr_id' => $action['ndr_id'],
            'action' => strtolower($action['action']),
            'remarks' => (!empty($action['remarks'])) ? $action['remarks'] : '',
            'attempt' => $ndr->total_attempts,
            'source' => $action['source'],
            're_attempt_date' => (!empty($action['re_attempt_date'])) ? $action['re_attempt_date'] : '0',
            'customer_details_name' => (!empty($action['customer_details_name'])) ? $action['customer_details_name'] : '',
            'customer_details_address_1' => (!empty($action['customer_details_address_1'])) ? $action['customer_details_address_1'] : '',
            'customer_details_address_2' => (!empty($action['customer_details_address_2'])) ? $action['customer_details_address_2'] : '',
            'customer_contact_phone' => (!empty($action['customer_contact_phone'])) ? $action['customer_contact_phone'] : '',
        );
          
        $last_insert_id = $this->insert_action($save);
        
        $update_ndr = array(
            'total_attempts' => $save['attempt'],
            'last_action_by' => $save['source'],
            'last_action' => $action['action'],
            'latest_remarks' => $save['remarks'],
            'last_event' => time(),
            'last_action_id' => $last_insert_id
        );

        $this->update($save['ndr_id'], $update_ndr);

        return true;
    }

    function sendNdrToCourier($courier_ids = false, $courier_name = false, $time = 'morning')
    {
        if (!$courier_ids)
            return false;

        $courier_name = strtolower($courier_name);

        $filter = array(
            'courier_ids' => $courier_ids,
            'start_date' => ($time == 'morning') ? strtotime('yesterday 16:00') : strtotime('today 06:00'),
            'end_date' => time(),
        );


        $response_list = $this->ndrsheetforCourier($filter);

        $unactioned_list = array();
        if (strtolower(trim($time)) == "evening") {
            $unactioned_list = $this->unactioned_ndrsheetforCourier($filter);
        }

        if (empty($response_list) && empty($unactioned_list))
            return false;

        $filename = date('d-M-y') . '_' . time() . rand(1111, 9999) . '.csv';

        $file = fopen('assets/ndr/' . $filename, 'w');
        $header = array(
            "AWB Number",
            "Action",
            "Action By",
            "Remarks",
            "Shipment Status",
            "Last Delivery Attempt Date",
            "Non Delivery Reason",
            "No. Of Delivery Attempts",
            "Customer Name",
            "Customer Address",
            "Customer City",
            "Customer State",
            "Customer Pincode",
            "Customer Contact No.",
            "User Type"
        );
        fputcsv($file, $header);

        foreach ($response_list as $response) {
            $row = array(
                $response->awb_number,
                strtoupper($response->ndr_action),
                strtoupper($response->ndr_source),
                $response->ndr_remarks,
                ucwords($response->ship_status),
                ($response->last_attempt_date > 0) ? date('Y-m-d', $response->last_attempt_date) : '',
                $response->last_ndr_reason,
                $response->delivery_attempt_count,
                (!empty($response->customer_details_name) ? ucwords($response->customer_details_name) : ucwords($response->shipping_fname . ' ' . $response->shipping_lname)),
                (!empty($response->change_customer_address) ? $response->change_customer_address : $response->shipping_address . ', ' . $response->shipping_address_2),
                ucwords($response->shipping_city),
                ucwords($response->shipping_state),
                $response->shipping_zip,
                (!empty($response->customer_contact_phone) ? $response->customer_contact_phone : $response->shipping_phone),
                ucwords($response->ndr_action_type),
            );
            fputcsv($file, $row);
        }
        //the NDR list, which aren't taken any action by seller 
        
        foreach ($unactioned_list as $list) {
            $remarks = "Re-attempt Date: ".date("Y-m-d", strtotime('tomorrow'));
            
            $row = array(
                $list->awb_number,
                strtoupper($list->ndr_action),
                strtoupper($list->ndr_source),
                $remarks,
                ucwords($list->ship_status),
                ($list->last_attempt_date > 0) ? date('Y-m-d', $list->last_attempt_date) : '',
                $list->last_ndr_reason,
                $list->delivery_attempt_count,
                ucwords($list->shipping_fname . ' ' . $list->shipping_lname),
                $list->shipping_address . ', ' . $list->shipping_address_2,
                ucwords($list->shipping_city),
                ucwords($list->shipping_state),
                $list->shipping_zip,
                $list->shipping_phone,
                ucwords($list->ndr_action_type),
            );
            fputcsv($file, $row);
        }

        fclose($file);

        $send_to = (!empty($this->CI->config->item('courier_ndr_email')[$courier_name])) ? $this->CI->config->item('courier_ndr_email')[$courier_name] : false;

        if (!$send_to)
            return false;

        $this->CI->load->library('email_lib');
        $email = new Email_lib();
        $email->attach('assets/ndr/' . $filename);

        $email->to($send_to);
        $email->from($this->CI->config->item('courier_ndr_from_email'));
        $email->set_cc($this->CI->config->item('courier_ndr_email_cc'));

        $email->subject("[" . strtoupper($courier_name) . "] NDR Actions Date " . date('d-M-Y') . " Client deltagloabal");
        $email->message($this->CI->load->view('emails/ndr_courier_email', false, true));
        $email->send();

        unlink('assets/ndr/' . $filename);
        return true;
    }

    // function pushNdrActionToCourier($ndr_id = false)
    // {
    //     if (!$ndr_id)
    //         return false;

    //     $ndr = $this->getByID($ndr_id);

    //     if (empty($ndr)) {
    //         $this->error = 'No record found';
    //         return false;
    //     }

    //     if (!in_array($ndr->last_action_by, array('seller', 'buyer'))) {
    //         $this->error = 'Invalid action source';
    //         return false;
    //     }


    //     if (!in_array($ndr->last_action, array('change phone', 'change address', 're-attempt'))) {
    //         $this->error = 'Action not supported in API';
    //         return false;
    //     }

    //     $action = $this->getNdrLastAction($ndr_id);

    //     if (empty($action)) {
    //         $this->error = 'Action not available';
    //         return false;
    //     }

    //     if ($action->push_ndr_status == '1') {
    //         $this->error = 'Already pushed';
    //         return false;
    //     }


    //     $this->CI->load->library('shipping_lib');
    //     $shipment = $this->CI->shipping_lib->getByID($ndr->shipment_id);

    //     if (empty($shipment)) {
    //         $this->error = 'Shipment not found';
    //         return false;
    //     }

    //     $this->CI->load->library('orders_lib');
    //     $order = $this->CI->orders_lib->getByID($shipment->order_id);

    //     if (empty($shipment)) {
    //         $this->error = 'Order not found';
    //         return false;
    //     }

    //     $ndr_data = array(
    //         'awb_number' => $shipment->awb_number,
    //         'action' => $action->action,
    //         'remarks' => $action->remarks,
    //         're_attempt_date' => $action->re_attempt_date,
    //         'change_name' => $action->customer_details_name,
    //         'change_address_1' => $action->customer_details_address_1,
    //         'change_address_2' => $action->customer_details_address_2,
    //         'change_phone' => $action->customer_contact_phone,
    //         'shipping_city' => $order->shipping_city,
    //         'shipping_state' => $order->shipping_state,
    //         'shipping_pincode' => $order->shipping_zip,
    //         'shipping_phone' => $order->shipping_phone,
    //     );

    //     switch ($shipment->courier_id) {
    //         case '1': //delhivery air
    //             $this->CI->load->library('shipping/delhivery');
    //             $delhivery = new Delhivery();
    //             if (!$ndr_push = $delhivery->pushNDRAction($ndr_data))
    //                 $this->error = $delhivery->get_error();

    //             break;
    //         case '6': //delhivery surface
    //             $this->CI->load->library('shipping/delhivery');
    //             $delhivery = new Delhivery(array('mode' => 'surface'));
    //             if (!$ndr_push = $delhivery->pushNDRAction($ndr_data))
    //                 $this->error = $delhivery->get_error();

    //             break;
    //         case '7': //delhivery Surface 2 kg
    //             $this->CI->load->library('shipping/delhivery');
    //             $delhivery = new Delhivery(array('mode' => 'surface_2'));
    //             if (!$ndr_push = $delhivery->pushNDRAction($ndr_data))
    //                 $this->error = $delhivery->get_error();

    //             break;
    //         case '11': //delhivery Surface 5 kg
    //             $this->CI->load->library('shipping/delhivery');
    //             $delhivery = new Delhivery(array('mode' => 'surface_5'));
    //             if (!$ndr_push = $delhivery->pushNDRAction($ndr_data))
    //                 $this->error = $delhivery->get_error();

    //             break;
    //         case '13': //delhivery Surface 10 kg
    //             $this->CI->load->library('shipping/delhivery');
    //             $delhivery = new Delhivery(array('mode' => 'surface_10'));
    //             if (!$ndr_push = $delhivery->pushNDRAction($ndr_data))
    //                 $this->error = $delhivery->get_error();

    //             break;
    //         case '35': //delhivery Surface 20 kg
    //             $this->CI->load->library('shipping/delhivery');
    //             $delhivery = new Delhivery(array('mode' => 'surface_20'));
    //             if (!$ndr_push = $delhivery->pushNDRAction($ndr_data))
    //                 $this->error = $delhivery->get_error();

    //             break;
    //         case '5': //Bluedart Express
    //         case '76': //bluedart ros
    //             $this->CI->load->library('shipping/bluedart');
    //             $bluedart = new Bluedart();
    //             if (!$ndr_push = $bluedart->pushNDRAction($ndr_data))
    //                 $this->error = $bluedart->get_error();

    //             break;
    //         case '24': //bluedart VH
    //         case '77': //bluedart ros IN
    //             $this->CI->load->library('shipping/bluedart');
    //             $bluedart = new Bluedart(array('mode' => 'bluedart_24'));
    //             if (!$ndr_push = $bluedart->pushNDRAction($ndr_data))
    //                 $this->error = $bluedart->get_error();

    //             break;
    //         case '12': //bluedart express
    //             $this->CI->load->library('shipping/bluedart_express');
    //             $bluedart = new Bluedart_express();
    //             if (!$ndr_push = $bluedart->pushNDRAction($ndr_data))
    //                 $this->error = $bluedart->get_error();

    //             break;
    //         case '10': //Ecom EXpress
    //         case '26': //Ecom ROS
    //             $this->CI->load->library('shipping/ecom');
    //             $ecom = new Ecom();
    //             if (!$ndr_push = $ecom->pushNDRAction($ndr_data))
    //                 $this->error = $ecom->get_error();

    //             break;
    //         case '15': //Ekart
    //         case '25': //Ekart 1 KG
    //         case '27': //Ekart 2 KG
    //         case '28': //Ekart 5 KG
    //         case '60': //Ekart 10 KG
    //         case '61': //Ekart 3 KG
    //             $this->CI->load->library('shipping/ekart');
    //             $ekart = new Ekart();
    //             if (!$ndr_push = $ekart->pushNDRAction($ndr_data))
    //                 $this->error = $ekart->get_error();
    //             break;
    //         case '158': //Ekart Priority 1 KG
    //             $this->CI->load->library('shipping/ekart');
    //             $ekart = new Ekart(array('mode' => 'priority_1_kg'));
    //             if (!$ndr_push = $ekart->pushNDRAction($ndr_data))
    //                 $this->error = $ekart->get_error();
    //             break;
    //         case '3': //xpressbees air
    //             $arrMode = array('mode' => 'air');
    //             $this->CI->load->library('shipping/xpressbees_new');
    //             $xb = new Xpressbees_new($arrMode);
    //             if (!$ndr_push = $xb->pushNDRAction($ndr_data))
    //                 $this->error = $xb->get_error();
    //             break;
    //         case '14': //Xpressbees Surface
    //         case '163': //Xpressbees Surface
    //             $arrMode = array('mode' => 'surface');
    //             $this->CI->load->library('shipping/xpressbees_new');
    //             $xb = new Xpressbees_new($arrMode);
    //             if (!$ndr_push = $xb->pushNDRAction($ndr_data))
    //                 $this->error = $xb->get_error();
    //             break;
    //         case '41': //Xpressbees 20KG
    //             $arrMode = array('mode' => '20_kg');
    //             $this->CI->load->library('shipping/xpressbees_new');
    //             $xb = new Xpressbees_new($arrMode);
    //             if (!$ndr_push = $xb->pushNDRAction($ndr_data))
    //                 $this->error = $xb->get_error();
    //             break;
    //         case '42': //Xpressbees 1KG
    //             $arrMode = array('mode' => '1_kg');
    //             $this->CI->load->library('shipping/xpressbees_new');
    //             $xb = new Xpressbees_new($arrMode);
    //             if (!$ndr_push = $xb->pushNDRAction($ndr_data))
    //                 $this->error = $xb->get_error();
    //             break;
    //         case '45': //Xpressbees 2KG
    //             $arrMode = array('mode' => '2_kg');
    //             $this->CI->load->library('shipping/xpressbees_new');
    //             $xb = new Xpressbees_new($arrMode);
    //             if (!$ndr_push = $xb->pushNDRAction($ndr_data))
    //                 $this->error = $xb->get_error();
    //             break;
    //         case '46': //Xpressbees 5KG
    //             $arrMode = array('mode' => '5_kg');
    //             $this->CI->load->library('shipping/xpressbees_new');
    //             $xb = new Xpressbees_new($arrMode);
    //             if (!$ndr_push = $xb->pushNDRAction($ndr_data))
    //                 $this->error = $xb->get_error();
    //             break;
    //         case '47': //Xpressbees 10KG
    //             $arrMode = array('mode' => '10_kg');
    //             $this->CI->load->library('shipping/xpressbees_new');
    //             $xb = new Xpressbees_new($arrMode);
    //             if (!$ndr_push = $xb->pushNDRAction($ndr_data))
    //                 $this->error = $xb->get_error();
    //             break;
    //         case '30': //DTDC Surface 1 KG
    //             $this->CI->load->library('shipping/dtdc_new');
    //             $dtdc = new Dtdc_new(array('mode' => '1_kg'));
    //             if (!$ndr_push = $dtdc->pushNDRAction($ndr_data))
    //                 $this->error = $dtdc->get_error();
    //             break;
    //         case '31': //DTDC Surface 10 KG
    //             $this->CI->load->library('shipping/dtdc_new');
    //             $dtdc = new Dtdc_new(array('mode' => '10_kg'));
    //             if (!$ndr_push = $dtdc->pushNDRAction($ndr_data))
    //                 $this->error = $dtdc->get_error();
    //             break;
    //         case '69': //DTDC Surface 3 KG
    //             $this->CI->load->library('shipping/dtdc_new');
    //             $dtdc = new Dtdc_new(array('mode' => '3_kg'));
    //             if (!$ndr_push = $dtdc->pushNDRAction($ndr_data))
    //                 $this->error = $dtdc->get_error();
    //             break;
    //         case '70': //DTDC Surface 20 KG
    //             $this->CI->load->library('shipping/dtdc_new');
    //             $dtdc = new Dtdc_new(array('mode' => '20_kg'));
    //             if (!$ndr_push = $dtdc->pushNDRAction($ndr_data))
    //                 $this->error = $dtdc->get_error();
    //             break;
    //         case '79': //DTDC Air 0.5 KG
    //         case '81': //DTDC Premium 0.5 KG
    //             $this->CI->load->library('shipping/dtdc_new');
    //             $dtdc = new Dtdc_new(array('mode' => 'air'));
    //             if (!$ndr_push = $dtdc->pushNDRAction($ndr_data))
    //                 $this->error = $dtdc->get_error();
    //             break;
    //         case '80': //DTDC Surface 0.5 KG
    //             $this->CI->load->library('shipping/dtdc_new');
    //             $dtdc = new Dtdc_new(array('mode' => 'surface'));
    //             if (!$ndr_push = $dtdc->pushNDRAction($ndr_data))
    //                 $this->error = $dtdc->get_error();
    //             break;
    //         case '88': //DTDC Surface 5 KG
    //             $this->CI->load->library('shipping/dtdc_new');
    //             $dtdc = new Dtdc_new(array('mode' => '5_kg'));
    //             if (!$ndr_push = $dtdc->pushNDRAction($ndr_data))
    //                 $this->error = $dtdc->get_error();
    //             break;
    //         case '133': //Xpressbees CRED
    //             $arrMode = array('mode' => 'cred');
    //             $this->CI->load->library('shipping/xpressbees_new');
    //             $xb = new Xpressbees_new($arrMode);
    //             if (!$ndr_push = $xb->pushNDRAction($ndr_data))
    //                 $this->error = $xb->get_error();
    //             break;
    //         case '134': //DTDC Surface 5 KG
    //             $this->CI->load->library('shipping/dtdc_new');
    //             $dtdc = new Dtdc_new(array('mode' => '5_kg_new'));
    //             if (!$ndr_push = $dtdc->pushNDRAction($ndr_data))
    //                 $this->error = $dtdc->get_error();
    //             break;
    //         case '135': //DTDC Surface 20 KG
    //             $this->CI->load->library('shipping/dtdc_new');
    //             $dtdc = new Dtdc_new(array('mode' => '20_kg_new'));
    //             if (!$ndr_push = $dtdc->pushNDRAction($ndr_data))
    //                 $this->error = $dtdc->get_error();
    //             break;
    //         case '157': //DTDC Surface 2 KG
    //             $this->CI->load->library('shipping/dtdc_new');
    //             $dtdc = new Dtdc_new(array('mode' => 'cred_2_kg'));
    //             if (!$ndr_push = $dtdc->pushNDRAction($ndr_data))
    //                 $this->error = $dtdc->get_error();
    //             break;
    //         default:
    //             $this->error = 'Courier API not available';
    //             return false;
    //     }

    //     if (empty($ndr_push)) {
    //         $save = array(
    //             'push_ndr_status' => '2',
    //             'push_ndr_message' => $this->error,
    //             'push_time' => time()
    //         );
    //     } else {
    //         $save = array(
    //             'push_ndr_status' => '1',
    //             'push_ndr_message' => $ndr_push['message'],
    //             'push_time' => time()
    //         );
    //     }

    //     //save ndr action
    //     $this->update_action($action->id, $save);
    //     $this->update($ndr_id, array('last_action_id'=>$action->id));

    //     $save_ndr = array(
    //         'push_api_status' => $save['push_ndr_status'],
    //     );

    //     $this->update($ndr->id, $save_ndr);

    //     return true;
    // }

     function pushNdrActionToCourier($ndr_id = false)
    {
        if (!$ndr_id)
            return false;

        $ndr = $this->getByID($ndr_id);

        if (empty($ndr)) {
            $this->error = 'No record found';
            return false;
        }

        if (!in_array($ndr->last_action_by, array('seller', 'buyer'))) {
            $this->error = 'Invalid action source';
            return false;
        }


        if (!in_array($ndr->last_action, array('change phone', 'change address', 're-attempt'))) {
            $this->error = 'Action not supported in API';
            return false;
        }

        $action = $this->getNdrLastAction($ndr_id);

        if (empty($action)) {
            $this->error = 'Action not available';
            return false;
        }

        if ($action->push_ndr_status == '1') {
            $this->error = 'Already pushed';
            return false;
        }


        $this->CI->load->library('shipping_lib');
        $shipment = $this->CI->shipping_lib->getByID($ndr->shipment_id);

        if (empty($shipment)) {
            $this->error = 'Shipment not found';
            return false;
        }

        $this->CI->load->library('orders_lib');
        $order = $this->CI->orders_lib->getByID($shipment->order_id);

        if (empty($shipment)) {
            $this->error = 'Order not found';
            return false;
        }

        $ndr_data = array(
        'awb_number'        => $shipment->awb_number,
        'action'            => $action->action ?? '',
        'remarks'           => $action->remarks ?? '',  // <-- Safe handling
        're_attempt_date'   => $action->re_attempt_date ?? '',
        'change_name'       => $action->customer_details_name ?? '',
        'change_address_1'  => $action->customer_details_address_1 ?? '',
        'change_address_2'  => $action->customer_details_address_2 ?? '',
        'change_phone'      => $action->customer_contact_phone ?? '',
        'shipping_city'     => $order->shipping_city ?? '',
        'shipping_state'    => $order->shipping_state ?? '',
        'shipping_pincode'  => $order->shipping_zip ?? '',
        'shipping_phone'    => $order->shipping_phone ?? '',
    );

    switch ($shipment->courier_id) {
        //  Delhivery Cases
        case '37': // delhivery air
        case '33': // delhivery surface
        case '34': // delhivery Surface 2 kg
        case '35': // delhivery Surface 5 kg
        case '36': // delhivery Surface 10 kg
        $this->CI->load->library('shipping/delhivery');
        $modes = [
            '37' => 'delhivery_air',
            '33' => 'delhivery_surface',
            '34' => 'delhivery_surface_2kg',
            '35' => 'delhivery_surface_5kg',
            '36' => 'delhivery_surface_10kg'
        ];
        $delhivery = new Delhivery(['mode' => $modes[$shipment->courier_id]]);
        if (!$ndr_push = $delhivery->pushNDRAction($ndr_data)) {
            $this->error = $delhivery->get_error();
        }
        break;

        //  Xpressbees Cases
        case '28': // Xpressbees Surface
        case '32': // Xpressbees 20KG
        case '29': // Xpressbees 2KG
        case '30': // Xpressbees 5KG
        case '31': // Xpressbees 10KG
        $this->CI->load->library('shipping/xpressbees');
        $xb_modes = [
            '28' => 'xpressbees_surface',
            '32' => 'xpressbees_surface_20kg',
            '29' => 'xpressbees_surface_2kg',
            '30' => 'xpressbees_surface_5kg',
            '31' => 'xpressbees_surface_10kg'
        ];
        $xb = new Xpressbees(['mode' => $xb_modes[$shipment->courier_id]]);
        if (!$ndr_push = $xb->pushNDRAction($ndr_data)) {
            $this->error = $xb->get_error();
        }
        break;
        
	// ================== DAAKITGO CASES ==================
        case '1124':
        case '1125':
        case '1126':
        case '1127':
        case '1128':
        case '1129':

            $this->CI->load->library('shipping/DaakitGo');
            $dg = new DaakitGo();

            if (!$ndr_push = $dg->pushNDRAction($ndr_data)) {
                $this->error = $dg->get_error();
            }
            break;
        //  Fship Cases
        case '1': case '2': case '3': case '4': case '5':
        case '6': case '7': case '8': case '9': case '10':
        case '11': case '12': case '13': case '14': case '15':
        case '16': case '17': case '18': case '19': case '20':
        case '21':
        $this->CI->load->library('shipping/aggregator/fship');
        $fship = new Fship();

        if ($shipment && !empty($shipment->shipment_info_1)) {
            $shipment_info = json_decode($shipment->shipment_info_1, true);
            if (isset($shipment_info['apiorderid'])) {
                $ndr_data['api_order_id'] = $shipment_info['apiorderid'];
            }
        }

        if (!$ndr_push = $fship->pushNDRAction($ndr_data)) {
            $this->error = $fship->get_error();
        }
        break;

        default:
            $this->error = 'Courier API not available';
            return false;
    }


        if (empty($ndr_push)) {
            $save = array(
                'push_ndr_status' => '2',
                'push_ndr_message' => $this->error,
                'push_time' => time()
            );
        } else {
            $save = array(
                'push_ndr_status' => '1',
                'push_ndr_message' => $ndr_push['message'],
                'push_time' => time()
            );
        }

        //save ndr action
        $this->update_action($action->id, $save);
        $this->update($ndr_id, array('last_action_id'=>$action->id));

        $save_ndr = array(
            'push_api_status' => $save['push_ndr_status'],
        );

        $this->update($ndr->id, $save_ndr);

        return true;
    }

    function pushEdShipmentToCourier($ed_id = false)
    {
        if (!$ed_id)
            return false;

        $ed_data = $this->getEdShipmentByID($ed_id);

        if (empty($ed_data)) {
            $this->error = 'No record found';
            return false;
        }

        if ($ed_data->status == '1') {
            $this->error = 'Already pushed';
            return false;
        }

        $ndr_data = array(
            'awb_number' => $ed_data->awb_number,
            'action' => 'escalation delivery'
        );

        switch ($ed_data->courier_id) {
            case '5': //Bluedart Express
            case '76': //Bluedart ros
                $this->CI->load->library('shipping/bluedart');
                $bluedart = new Bluedart();
                if (!$ndr_push = $bluedart->pushNDRAction($ndr_data))
                    $this->error = $bluedart->get_error();

                break;
            case '24': //Bluedart VH
            case '77': //Bluedart ros IN
                $this->CI->load->library('shipping/bluedart');
                $bluedart = new Bluedart(array('mode' => 'bluedart_24'));
                if (!$ndr_push = $bluedart->pushNDRAction($ndr_data))
                    $this->error = $bluedart->get_error();

                break;
            case '12': //Bluedart express
                $this->CI->load->library('shipping/bluedart_express');
                $bluedart = new Bluedart_express();
                if (!$ndr_push = $bluedart->pushNDRAction($ndr_data))
                    $this->error = $bluedart->get_error();

                break;
            default:
                $this->error = 'Courier API not available';
                return false;
        }

        if (empty($ndr_push)) {
            $save = array(
                'status' => '1',
                'api_status' => $this->error,
                'modified' => time()
            );
        } else {
            $save = array(
                'status' => '1',
                'api_status' => $ndr_push['message'],
                'modified' => time()
            );
        }

        $this->getEdShipmentByAwbNumber($ed_data->awb_number, $save);

        return true;
    }
}
