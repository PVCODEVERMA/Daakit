<?php

namespace App\Lib\Logs;

use App\Lib\BaseLib;

class Log extends BaseLib
{
    protected $user_id;
    protected $entity_type;
    protected $action_type;
    protected $ref_id;
    protected $notes;

    public function __construct()
    {
        parent::__construct();
        $this->user_id = '0';
        $this->entity_type = '';
        $this->action_type = '';
        $this->notes = '';
    }

    function setUserID($value = false)
    {
        $this->user_id = $value;
    }

    function setEntityType($value = false)
    {
        $this->entity_type = $value;
    }

    function setActionType($value = false)
    {
        $this->action_type = $value;
    }

    function setRefID($value = false)
    {
        $this->ref_id = $value;
    }

    function setNotes($value = false)
    {
        $this->notes = $value;
    }

    function create($user_id = false, $ref_id = false, $notes = '')
    {
        $this->setUserID($user_id);
        $this->setRefID($ref_id);
        $this->setNotes($notes);

        $this->setActionType('create');
        return $this->save();
    }

    function update($user_id = false, $ref_id = false, $notes = '')
    {
        $this->setUserID($user_id);
        $this->setRefID($ref_id);
        $this->setNotes($notes);
        
        $this->setActionType('update');
        return $this->save();
    }

    function delete($user_id = false, $ref_id = false, $notes = '')
    {
        $this->setUserID($user_id);
        $this->setRefID($ref_id);
        $this->setNotes($notes);
        
        $this->setActionType('delete');
        return $this->save();
    }


    function save()
    {
        $this->CI->load->model('logs_model');
        $save = array(
            'user_id' => $this->user_id,
            'action_type' => $this->action_type,
            'log_data' => json_encode(array('entity_type' => $this->entity_type,'ref_id' => $this->ref_id, 'notes' => $this->notes))
        );
        $this->CI->logs_model->insert($save);
        return $this;
    }
}
