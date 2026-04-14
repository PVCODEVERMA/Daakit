<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ndr_lib extends MY_lib {

    public function __construct() {
        parent::__construct();

        $this->CI->load->model('admin/ndr_model');
    }

    public function __call($method, $arguments) {
        if (!method_exists($this->CI->ndr_model, $method)) {
            throw new Exception('Undefined method ndr_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->ndr_model, $method], $arguments);
    }

    function manageNDRAgainstShipStatus($shipment_id = false, $event = false) {
        if (!$shipment_id || empty($event))
            return false;

        $this->CI->load->library('shipping_lib');
        $shipment = $this->CI->shipping_lib->getByID($shipment_id);

        if (empty($shipment))
            return false;

        ////check if shipment is not exception and no existing record of ndr
        $ndr = $this->getByShippingID($shipment_id);

        if (empty($ndr) && $event['ship_status'] != 'exception')  //current status is not exception and no existing record of ndr
            return false;

        $this->CI->load->library('orders_lib');
        $order = $this->CI->orders_lib->getByID($shipment->order_id);

        //if existing ndr then update else create new ndr
        if (empty($ndr)) {
            //create new ndr
            $save = array(
                'user_id' => $order->user_id,
                'order_id' => $shipment->order_id,
                'shipment_id' => $shipment_id,
            );

            $ndr_id = $this->insert($save);
        } else {
            $ndr_id = $ndr->id;
        }

        $this->updateNDRCourierAction($ndr_id, $event);
    }

    function updateNDRCourierAction($ndr_id = false, $event = false) {
        if (!$ndr_id || empty($event))
            return false;

        //get last action by courier
        $last_action = $this->ndrCourierLastAction($ndr_id);
        if (empty($last_action)) { //no previous action for this ndr
            $save = array(
                'ndr_id' => $ndr_id,
                'action' => 'ndr',
                'remarks' => $event['message'],
                'attempt' => 1,
                'source' => 'courier',
                'event_time' => $event['event_time'],
            );
            $last_action_id = $this->insert_action($save);
        } elseif ($event['ship_status'] == 'exception' && $event['event_time'] > $last_action->event_time) {
            //another exception received
            $save = array(
                'ndr_id' => $ndr_id,
                'action' => 'ndr',
                'remarks' => $event['message'],
                'attempt' => $last_action->attempt + 1,
                'source' => 'courier',
                'event_time' => $event['event_time'],
            );
            $last_action_id = $this->insert_action($save);
        }
        $this->update($save['ndr_id'], array('ndr_id' => $last_action_id));
        return true;
    }

    function AddNDRAction($action = false) {
        if (empty($action['ndr_id']) || empty($action['source'])) {
            $this->error = 'Invalid Request';
            return false;
        }

        //get latest action bt courier
        $courier_last_action = $this->ndrCourierLastAction($action['ndr_id']);
        if (empty($courier_last_action)) {
            $this->error = 'No Courier Exception Available';
            return false;
        }

        $save = array(
            'ndr_id' => $action['ndr_id'],
            'action' => $action['action'],
            'remarks' => $action['remarks'],
            'attempt' => $courier_last_action->attempt,
            'source' => $action['source'],
        );

        $last_action_id = $this->insert_action($save);
        
        $this->update($save['ndr_id'], array('last_action_id', $last_action_id));
        return $last_action_id;
    }

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
        $this->CI->load->library('shipping/Delhivery');
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
        $this->CI->load->library('shipping/Xpressbees');
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
        $this->CI->load->library('shipping/aggregator/Fship');
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

}

?>
