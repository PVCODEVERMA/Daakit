<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tags extends User_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('tags_lib');
    }

    function orders($action = 'add')
    {
        $id = $this->input->post('id');
       
        $action = strtolower($action);

        $this->data['action'] = $action;
        $this->data['type'] = 'orders';

        $this->load->library('orders_lib');
        $existing_records = $this->orders_lib->getAllTags($this->user->account_id);
        
        $existing_tags = array();
        if (!empty($existing_records)) {
            foreach ($existing_records as $tags){
                $existing_tags[] = $tags->tag;
            }
        }

        $applied_tags = array();
        if(!empty($id)){  
            $order = $this->orders_lib->getByID($id);
            $applied_tags = $order->applied_tags;
        }
        
        $this->data['tags'] = $existing_tags;
        $this->data['applied_tags'] = $applied_tags;
        $this->data['id'] = $id;
        if(!empty($id)){
            $this->layout('tags/single_add_remove_tags', 'NONE');
        }else{
            $this->layout('tags/add_remove_tags', 'NONE');
        }
           
       
       
    }

    function fetch_tags()
    {
        $id = $this->input->post('id');
        $values = $this->input->post('values');
        $this->load->library('orders_lib');
        $fatch_rec = $this->orders_lib->fetchAllTags($this->user->account_id,$values);
        if(!empty($fatch_rec))
        {
            echo ucfirst($fatch_rec[0]->tag);
        }
        else
        {
            echo  "0";
        }
    }

    function shipment($action = 'add')
    {
        $action = strtolower($action);

        $id = $this->input->post('id');

        $this->data['action'] = $action;
        $this->data['type'] = 'shipments';

        $this->load->library('shipping_lib');
        $existing_records = $this->shipping_lib->getAllTags($this->user->account_id);

        $applied_tags = array();
        if(!empty($id)){  
             $shipments = $this->shipping_lib->getByID($id);
             $applied_tags = $shipments->applied_tags;
        }
       
        $existing_tags = array();
        if (!empty($existing_records->applied_tags)) {
            $existing_tags = explode(',', $existing_records->applied_tags);
            $existing_tags = array_unique($existing_tags);
        }

        $this->data['tags'] = $existing_tags;
        $this->data['applied_tags'] = $applied_tags;
        $this->data['id'] = $id;

        if(!empty($id)){
            $this->layout('tags/single_add_remove_tags', 'NONE');
        }else{
            $this->layout('tags/add_remove_tags', 'NONE');
        }
  
    }

    function ndr($action = 'add')
    {
        $action = strtolower($action);

        $this->data['action'] = $action;
        $this->data['type'] = 'ndr';

        $this->load->library('ndr_lib');
        $existing_records = $this->ndr_lib->getAllTags($this->user->account_id);

        $existing_tags = array();
        if (!empty($existing_records->applied_tags)) {
            $existing_tags = explode(',', $existing_records->applied_tags);
            $existing_tags = array_unique($existing_tags);
        }

        $this->data['tags'] = $existing_tags;

        $this->layout('tags/add_remove_tags', 'NONE');
    }


    function abandoned($action = 'add')
    {
        $action = strtolower($action);

        $this->data['action'] = $action;
        $this->data['type'] = 'abandoned';


        $this->load->library('apps/checkouts_lib');
        $existing_records = $this->checkouts_lib->getAllTags($this->user->account_id);

        $existing_tags = array();
        if (!empty($existing_records->applied_tags)) {
            $existing_tags = explode(',', $existing_records->applied_tags);
            $existing_tags = array_unique($existing_tags);
        }

        $this->data['tags'] = $existing_tags;



        $this->layout('tags/add_remove_tags', 'NONE');
    }


    function add_remove()
    {
        $this->load->library('form_validation');

        $id = $this->input->post('employee_id');

        $config = array(
            array(
                'field' => 'action',
                'label' => 'Action',
                'rules' => 'trim|required|in_list[add,remove]'
            ),
            array(
                'field' => 'type',
                'label' => 'Type',
                'rules' => 'trim|required|in_list[orders,shipments,ndr,abandoned]'
            ),
            array(
                'field' => 'tags',
                'label' => 'Tags',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'selected_ids[]',
                'label' => 'Records',
                'rules' => 'trim|required'
            ),
        );


        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        $action = $this->input->post('action');
        $type = $this->input->post('type');
        $tags = explode(',', $this->input->post('tags'));
        $ids = $this->input->post('selected_ids');

        switch ($type) {
            case 'orders':
                    $this->tags_lib->addRemoveOrderTags($ids, $tags, $action, $this->user->account_id);
                break;
            case 'shipments':
                   $this->tags_lib->addRemoveShipmentTags($ids, $tags, $action, $this->user->account_id);
                break;
            case 'ndr':
                $this->tags_lib->addRemoveNDRTags($ids, $tags, $action, $this->user->account_id);
                break;
            case 'abandoned':
                $this->tags_lib->addRemoveAbandonenimgs($ids, $tags, $action, $this->user->account_id);
                break;
            default:
        }


        $this->data['json'] = array('success' => 'Tags Added');
        $this->layout(false, 'json');
        return;
    }


    function single_add_remove()
    {
        $this->load->library('form_validation');

        $id = $this->input->post('employee_id');

        $config = array(
            array(
                'field' => 'action',
                'label' => 'Action',
                'rules' => 'trim|required|in_list[add]'
            ),
            array(
                'field' => 'type',
                'label' => 'Type',
                'rules' => 'trim|required|in_list[orders,shipments,ndr,abandoned]'
            ),
            array(
                'field' => 'tags',
                'label' => 'Tags',
                'rules' => 'trim'
            ),
            array(
                'field' => 'selected_ids',
                'label' => 'Records',
                'rules' => 'trim|required'
            ),
        );


        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        $action = $this->input->post('action');
        $type = $this->input->post('type');
        $tags = explode(',', $this->input->post('tags'));
        $ids = $this->input->post('selected_ids');

        switch ($type) {
            case 'orders':
                       $this->tags_lib->addRemoveSingleOrderTags($ids, $tags, $action, $this->user->account_id);
                break;
            case 'shipments':
                      $this->tags_lib->addRemoveSingleShipmentTags($ids, $tags, $action, $this->user->account_id);
                break;
            default:
        }


        $this->data['json'] = array('success' => 'Tags Added');
        $this->layout(false, 'json');
        return;
    }

/**
 * 
 * 
 */

        function users($action = 'add')
        {
            $action = strtolower($action);

            $this->data['action'] = $action;
            $this->data['type'] = 'users';

            $this->load->library('user_lib');
            $existing_records = $this->user_lib->getAllTags($this->user->account_id);

            $existing_tags = array();
            
            if (!empty($existing_records)) {
                foreach ($existing_records as $tags){
                    $existing_tags[] = $tags->tag;
                }
            }

            $this->data['tags'] = $existing_tags;

            $this->layout('tags/user_tags', 'NONE');
        }



 function user_tag_add_remove()
    {
        $this->load->library('form_validation');

        $id = $this->input->post('employee_id');

        $config = array(
            array(
                'field' => 'action',
                'label' => 'Action',
                'rules' => 'trim|required|in_list[add,remove]'
            ),
            array(
                'field' => 'type',
                'label' => 'Type',
                'rules' => 'trim|required|in_list[users]'
            ),
            array(
                'field' => 'tags',
                'label' => 'Tags',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'selected_ids[]',
                'label' => 'Records',
                'rules' => 'trim|required'
            ),
        );


        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        $action = $this->input->post('action');
        $type = $this->input->post('type');
        $tags = explode(',', $this->input->post('tags'));
        $ids = $this->input->post('selected_ids');

        switch ($type) {
            case 'users':
                    $this->addRemoveUserTags($ids, $tags, $action, $this->user->account_id);
                    break;    
            default:
        }


        $this->data['json'] = array('success' => 'Tags Added');
        $this->layout(false, 'json');
        return;
    }


    function user_tag_add_remove_individual()
    {
        // $this->load->library('form_validation');

        $id = $this->input->post('employee_id');

        $config = array(
            array(
                'field' => 'action',
                'label' => 'Action',
                'rules' => 'trim|required|in_list[add,remove]'
            ),
            array(
                'field' => 'type',
                'label' => 'Type',
                'rules' => 'trim|required|in_list[users]'
            ),
        );


        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        $action = $this->input->post('action');
        $type = $this->input->post('type');
        $tags = explode(',', $this->input->post('tags'));
        $user_id = $this->input->post('user_id');

        switch ($type) {
            case 'users':
                    $this->addRemoveUserTagsIndividual($tags, $action, $user_id);
                    break;    
            default:
        }


        $this->data['json'] = array('success' => 'Tags Added');
        $this->layout(false, 'json');
        return;
    }



  function addRemoveUserTags($ids = array(), $tags = false, $action = 'add', $user_id = false)
    {
        if (empty($ids) || empty($tags))
            return false;

        $this->addNewUserTags($tags, $user_id);
        //fetchuserTags for these users
        $this->load->library('user_lib');

        foreach ($ids as $id) {
            $user = $this->user_lib->getByID($id);
            
            $existing_tags = explode(',', $user->applied_tags);
            if ($action == 'add')
                $new_values = array_unique(array_merge($existing_tags, $tags));
            else
          
            $new_values = array_diff($existing_tags, $tags);

            $new_values = array_map('trim', $new_values);
            $new_values = array_map('strtolower', $new_values);
            $new_values = array_filter($new_values);
             
            $save = array(
                'applied_tags' => implode(',', $new_values)
            );

            $this->user_lib->update($id, $save);
        }

        return true;
    }

    function addNewUserTags($tags = array(), $user_id = false)
    {
        if (empty($user_id) || empty($tags))
            return false;

        $this->load->library('user_lib');

        $tags = array_map('trim', $tags);
        $tags = array_map('strtolower', $tags);
        $tags = array_filter($tags);

        $existing_result = $this->user_lib->getAllTags($user_id, $tags);
      
        $existing_tags = array();
        foreach ($existing_result as $v) {
            $existing_tags[] = $v->tag;
        }
        $existing_tags = array_values($existing_tags);
        $new_values = array_diff($tags, $existing_tags);
        $new_values = array_filter($new_values);
     
        foreach ($new_values as $tag) {
            // $save = array('tag' => trim(strtolower($tag)), 'user_id' => $user_id);
            $save = array('tag' => trim(strtolower($tag)));
            $this->user_lib->insertUserTag($save);
        }
        return true;
    }


    function addRemoveUserTagsIndividual($tags = false, $action = 'add', $user_id = false)
    {
        if (empty($tags))
            return false;

        $this->addNewUserTagsIndividual($tags, $user_id);
        //fetchuserTags for these users
        $this->load->library('user_lib');

            $user = $this->user_lib->getByID($user_id);
            
            $existing_tags = explode(',', $user->applied_tags);
            if ($action == 'add')
                $new_values = array_unique(array_merge($existing_tags, $tags));
            else
          
            $new_values = array_diff($existing_tags, $tags);

            $new_values = array_map('trim', $new_values);
            $new_values = array_map('strtolower', $new_values);
            $new_values = array_filter($new_values);
             
            $save = array(
                'applied_tags' => implode(',', $new_values)
            );

            $this->user_lib->update($user_id, $save);

        return true;
    }

    function addNewUserTagsIndividual($tags = array(), $user_id = false)
    {
        if (empty($user_id) || empty($tags))
            return false;

        $this->load->library('user_lib');

        $tags = array_map('trim', $tags);
        $tags = array_map('strtolower', $tags);
        $tags = array_filter($tags);

        $existing_result = $this->user_lib->getAllTags($user_id, $tags);
      
        $existing_tags = array();
        foreach ($existing_result as $v) {
            $existing_tags[] = $v->tag;
        }
        $existing_tags = array_values($existing_tags);
        $new_values = array_diff($tags, $existing_tags);
        $new_values = array_filter($new_values);
     
        foreach ($new_values as $tag) {
            // $save = array('tag' => trim(strtolower($tag)), 'user_id' => $user_id);
            $save = array('tag' => trim(strtolower($tag)));
            $this->user_lib->insertUserTag($save);
        }
        return true;
    }

}
