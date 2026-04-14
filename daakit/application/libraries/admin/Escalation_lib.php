<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Escalation_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();
        $this->CI->load->model('admin/escalation_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->escalation_model, $method)) {
            throw new Exception('Undefined method escalation_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->escalation_model, $method], $arguments);
    }

    function mergeEscStatus($status = false, $last_action_by = false)
    {
        if (!$status)
            return false;
        if (!$last_action_by)
            return $status;

        $status = strtolower($status);
        $last_action_by = strtolower($last_action_by);

        if ($status == 'closed' && $last_action_by == 'seller')
            return 're-opened';

        if (!in_array($status, array('new', 'closed')) && $last_action_by == 'seller')
            return 'seller replied';

        return $status;
    }

    function create_escalation($user_id = false, $save = array())
    {
        if (!$user_id || empty($save))
            return false;

        $create = array(
            'user_id' => $user_id,
            'type' => !empty($save['type']) ? strtolower($save['type']) : '',
            'sub_type' => !empty($save['sub_type']) ? strtolower($save['sub_type']) : '',
            'ref_id' => !empty($save['ref_id']) ? strtolower($save['ref_id']) : '',
            'created_by' => !empty($save['action_by']) ? strtolower($save['action_by']) : '',
            'escalation_status' => 'new'
        );

        $escalation_id = $this->insert($create);

        if (!$escalation_id)
            return false;

        $action = array(
            'remarks' => $save['remarks'],
            'action_by' => $save['action_by'],
            'attachments' => !empty($save['attachments']) ? $save['attachments'] : '',
            'action_user_id' => !empty($save['action_user_id']) ? $save['action_user_id'] : '',
        );

        $this->submit_action($escalation_id, $action);
        return true;
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
            'action_by' => $save['action_by'],
            'attachments' => !empty($save['attachments']) ? $save['attachments'] : '',
            'action_user_id' => !empty($save['action_user_id']) ? $save['action_user_id'] : '',
            'esc_status' => $action_esc,
            'assign_to' => !empty($save['assign_to']) ? $save['assign_to'] : '',
        );
        //pr($update); die;

        $action_id = $this->insert_action($update);


        $update_esc = array(
            'last_action_id' => $action_id,
            'last_action_by' => $save['action_by']
        );
       

        if (!empty($save['status'])) {
            $update_esc['escalation_status'] = $save['status'];
        }

        if (!empty($save['assign_to'])) {
            $update_esc['assign_to'] = $save['assign_to'];
        }

        $this->updateStartLogDate($escalation_id, $update_esc);
        $this->updateCloseLogDate($escalation_id, $update_esc);

        $this->update($escalation_id, $update_esc);
        return $action_id;
    }

    function submit_action_int_esc($escalation_id = false, $save = array())
    { 
       $this->update_action($escalation_id,$save);
      
    }


    function updateCloseLogDate($escalation_id, $update_esc)
    {
        if (empty($escalation_id))
            return false;

        $save = array();
        if (isset($update_esc['escalation_status']) && $update_esc['escalation_status'] == 'closed') {
            $save['esc_closed_date'] = time();
        }

        if (empty($save))
            return false;

        $this->update_closed_log_date($escalation_id, $save);
        return true;
    }


    function updateStartLogDate($escalation_id, $update_esc)
    {
        if (empty($escalation_id))
            return false;

        $save = array();
        if (isset($update_esc['escalation_status']) && $update_esc['escalation_status'] != 'new') {
            $save['esc_started_date'] = time();
        }

        if (empty($save))
            return false;

        $this->update_start_log_date($escalation_id, $save);
        return true;
    }

    function close_weight_dispute($esc_id = false, $won_by = false, $final_weight  = false,  $user_id = false)
    {
        if (!$esc_id || !$won_by)
            return false;

        $esc = $this->getByID($esc_id);


        if (empty($esc) || $esc->type != 'weight' || empty($esc->ref_id))
            return false;

        $this->CI->load->library('admin/apply_weight');

        $apply_weight = new Apply_weight();
        $apply_weight->setShipmentID($esc->ref_id);
        $apply_weight->setWonBy($won_by);
        $apply_weight->setFinalWeight($final_weight);

        $weight_closed = $apply_weight->closeWeightDispute();

        //$weight_closed = $this->CI->weight_lib->closeWeightDispute($esc->ref_id, $won_by);

        if (!$weight_closed)
            return false;

        //close this escalation

        $message = 'Auto Closed - Dispute closed in favour of ' . $won_by;
        if ($final_weight)
            $message = 'Auto Closed - Dispute closed with final weight ' . $final_weight . 'g';

        $save = array(
            'remarks' => $message,
            'action_by' => 'delta',
            'status' => 'closed',
            'action_user_id' => $user_id,
        );
        $this->submit_action($esc_id, $save);

        return true;
    }
}
