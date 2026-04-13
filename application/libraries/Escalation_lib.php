<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Escalation_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();
        $this->CI->load->model('escalation_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->escalation_model, $method)) {
            throw new Exception('Undefined method escalation_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->escalation_model, $method], $arguments);
    }

    function create_escalation($user_id = false, $save = array())
    {
        if (!$user_id || empty($save))
            return false;

        $create = array(
            'user_id' => $user_id,
            'type' => !empty($save['type']) ? strtolower($save['type']) : '',
            'sub_type' => !empty($save['sub_type']) ? strtolower($save['sub_type']) : '',
            'subject' => !empty($save['subject']) ? strtolower($save['subject']) : '',
            'ref_id' => !empty($save['ref_id']) ? strtolower($save['ref_id']) : '',
            'created_by' => !empty($save['action_by']) ? strtolower($save['action_by']) : '',
            'raised_by_id' => !empty($save['created_by_id']) ? strtolower($save['created_by_id']) : '0',
            'assign_to' => !empty($save['assign_to']) ? $save['assign_to'] : '0',
            'escalation_status' => 'new'
        );

        $escalation_id = $this->insert($create);

        if (!$escalation_id)
            return false;

        $action = array(
            'remarks' => $save['remarks'],
            'action_by' => $save['action_by'],
            'escalation_status' => 'new',
            'attachments' => !empty($save['attachments']) ? $save['attachments'] : '',
            //'assign_to' => !empty($save['assign_to']) ? $save['assign_to'] : '0',
        );

        $this->submit_action($escalation_id, $action);
        return $escalation_id;
    }

    function submit_action($escalation_id = false, $save = array())
    {

        if (!$escalation_id || empty($save))
            return false;

        $action_esc = '';
        if (isset($save['escalation_status'])) {

            $action_esc = $save['escalation_status'];
        }
        if (!empty($save['status'])) {
            $action_esc = $save['status'];
        }

        $update = array(
            'escalation_id' => $escalation_id,
            'remarks' => $save['remarks'],
            'attachments' => !empty($save['attachments']) ? $save['attachments'] : '',
            'action_by' => $save['action_by'],
            'esc_status' => $action_esc,
            //'assign_to' => !empty($save['assign_to']) ? $save['assign_to'] : '0',
        );

        $action_id = $this->insert_action($update);

        $update_esc = array(
            'last_action_id' => $action_id,
            'last_action_by' => $save['action_by'],
        );

        if (!empty($save['status'])) {
            $update_esc['escalation_status'] = $save['status'];
        }

        $this->update($escalation_id, $update_esc);

        return $action_id;
    }

    function autoClosePickupTickets()
    {

        $escalations = $this->getPickupDoneEscalation();
        if (empty($escalations))
            return false;

        foreach ($escalations as $esc) {
            $update = array(
                'remarks' => 'Auto closed - Pickup Complete',
                'action_by' => 'delta',
                'status' => 'closed'
            );

            $this->submit_action($esc->escalation_id, $update);
        }

        return true;
    }

    function autoCloseShipmentTickets()
    {

        $escalations = $this->getShipmentDoneEscalation();
        if (empty($escalations))
            return false;


        foreach ($escalations as $esc) {
            $update = array(
                'remarks' => 'Auto closed - Shipment Delivered',
                'action_by' => 'delta',
                'status' => 'closed'
            );

            $this->submit_action($esc->escalation_id, $update);
        }

        return true;
    }

    function autoCloseEscalationTickets($shipment_id = false, $event = false)
    {
        if (empty($shipment_id) || empty($event))
            return false;

        $this->CI->load->library('shipping_lib');
        $shipping_info = $this->CI->shipping_lib->getShipmentByID($shipment_id);

        $escalations = $remarks = array();

        $ship_status = $event['ship_status'];
        switch ($ship_status) {
            case 'out for delivery':
                $sub_type = array('re-attempt', 'stuck in transit');

                $escalations = $this->getShipmentEscalationTickets($shipment_id, $sub_type);

                $msg = 'Hi,<br />We would like to inform you, We have taken action on your escalation and shipment is out for delivery today.';

                $remarks['re-attempt'] = $remarks['stuck in transit'] = $msg;
                break;

            case 'delivered':
                $sub_type = array('re-attempt', 'hold shipment', 'change payment type', 'forward stuck in transit', 'self collect - branch address required');

                $msg = 'Hi,<br />We would like to inform you, Shipment is delivered to the customer. Happy shipping with deltagloabal.';

                $remarks['re-attempt'] = $remarks['hold shipment'] = $remarks['change payment type'] = $remarks['forward stuck in transit'] = $remarks['self collect - branch address required'] = $msg;

                $escalations = $this->getShipmentEscalationTickets($shipment_id, $sub_type);
                break;

            case 'rto in transit':
                $sub_type = array('rto instruction');

                $msg = 'Hi,This is to inform you, Shipment is processed for RTO on your instructions. It will be delivered back to you as soon as possible.';

                $remarks['rto instruction'] = $msg;

                $escalations = $this->getShipmentEscalationTickets($shipment_id, $sub_type);
                break;

            case 'rto delivered':
                $sub_type = array('rto instruction', 'rto stuck in transit');

                $msg = 'Hi,This is to inform you, Shipment is processed for RTO on your instructions. It will be delivered back to you as soon as possible.';

                $remarks['rto instruction'] = $msg;

                $msg = 'Hi,<br /> We would like to inform you, Shipment is RTO delivered at your registered address. Happy shipping with deltagloabal.';

                $remarks['rto stuck in transit'] = $msg;

                $escalations = $this->getShipmentEscalationTickets($shipment_id, $sub_type);
                break;

            default:
                break;
        }

        if (empty($escalations))
            return false;

        foreach ($escalations as $key => $escalation) {
            $update = array(
                'remarks' => isset($remarks[$escalation->sub_type]) ? $remarks[$escalation->sub_type] : '',
                'action_by' => 'delta',
                'status' => 'closed'
            );

            $this->submit_action($escalation->escalation_id, $update);
        }

        return true;
    }
}
