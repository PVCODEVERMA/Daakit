<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Employees extends User_controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('user_lib');
        $this->userHasAccess('settings');
    }

    function index() {

        $employees = $this->user_lib->getChildUsers($this->user->account_id);

        $this->data['employees'] = $employees;
        $this->layout('employees/index');
    }

    function add_employee() {
        $this->load->library('form_validation');

        $id = $this->input->post('employee_id');

        $config = array(
            array(
                'field' => 'name',
                'label' => 'Employee Name',
                'rules' => 'trim|required|min_length[2]|max_length[40]|alpha_numeric_spaces'
            ),
            array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'trim|required|min_length[3]|max_length[200]|valid_email'
            ),
            array(
                'field' => 'phone',
                'label' => 'Phone No.',
                'rules' => 'trim|required|exact_length[10]|integer|greater_than[0]'
            ),
        );
        if (!empty($id)) {
            $config[] = array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|min_length[6]|max_length[50]'
            );
        } else {
            $config[] = array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|min_length[6]|max_length[50]'
            );
        }

        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        $permission = $this->input->post('permission');
    
        if (!empty($permission)) {
            $permission = implode(',', array_map('trim', $permission));
        }

        $save = array(
            'parent_id' => $this->user->account_id,
            'fname' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone'),
            'password' => $this->input->post('password'),
            'permissions' => $permission,
            'international_permission' => '1',
            'is_franchise' => 'yes',
            'status' => '1'
        );

        if (!empty($id)) {
            $employee = $this->user_lib->getByID($id);

            if (empty($employee) || $employee->parent_id != $this->user->account_id) {
                $this->data['json'] = array('error' => 'Invalid Access');
                $this->layout(false, 'json');
                return;
            }

            if (empty($save['password']))
                unset($save['password']);

            if (!$this->user_lib->update_user($id, $save)) {
                $this->data['json'] = array('error' => $this->user_lib->get_error());
                $this->layout(false, 'json');
                return;
            }
            $this->data['json'] = array('success' => 'Record Updated');
            $this->layout(false, 'json');
            return;
        } else {
            if (!$this->user_lib->create_user($save)) {
                $this->data['json'] = array('error' => $this->user_lib->get_error());
                $this->layout(false, 'json');
                return;
            }
            $this->data['json'] = array('success' => 'Employee Record Saved');
            $this->layout(false, 'json');
            return;
        }
    }


    function change_status(){
       if(isset($_POST['status'])){
            $id = $_POST['id'];
           $result = $this->user_lib->checkParentUser($id,$this->user->account_id);
            if($result){
              $save['status'] = $_POST['status'];
                if($this->user_lib->update_user($id, $save)){
                  $this->data['json'] = array('success' => 'Record Updated');
                  $this->layout(false, 'json');
                }  
            }else{
                  $this->data['json'] = array('success' => 'User not belong to this account');
                  $this->layout(false, 'json'); 

            }
            
       }
    }

    function editEmployee($id = false) {
        if (!$id)
            return false;

        $employee = $this->user_lib->getByID($id);

        if (empty($employee) || $employee->parent_id != $this->user->account_id) {
            return false;
        }

        $this->data['employee_info'] = $employee;

        $this->layout('employees/add_new_form', 'NONE');
    }

}
