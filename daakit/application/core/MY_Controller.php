<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends MX_Controller
{

    var $data;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('MY_model');
        $this->load->library(
            array(
                'MY_lib', 'auth', 'autoload_lib'
            )
        );

        $this->form_validation->CI = &$this;

        if (!empty($_GET['dbg']) && $_GET['dbg'] == 'yes') {
            error_reporting(-1);
            ini_set('display_errors', 1);
            define('DEBUG', 'yes');
        } else {
            define('DEBUG', 'no');
        }


        $this->data['page_title'] = 'deltagloabal';


        if (!empty($this->session->flashdata('success')))
            $this->data['success'] = $this->session->flashdata('success');

        if (!empty($this->session->flashdata('error')))
            $this->data['error'] = $this->session->flashdata('error');

        if (!empty($_GET['success_msg']))
            $this->data['success'] = $_GET['success_msg'];

        if (!empty($_GET['error_msg']))
            $this->data['error'] = $_GET['error_msg'];



        if (!empty($this->input->get('ref'))) {
            $this->load->helper('cookie');
            set_cookie('ref_id', $this->input->get('ref'), 1296000, '', '/', FALSE);
            set_cookie('utm_source', $this->input->get('referral'), 1296000, '', '/', FALSE);
        }

        $utm_source = $this->input->get('utm_source');
        if (!empty($this->input->get('ref_source')))
            $utm_source = $this->input->get('ref_source');


        if (!empty($utm_source)) {
            $this->load->helper('cookie');
            set_cookie('utm_source', $utm_source, 1296000, '', '/', FALSE);
        }
    }

    protected function validateAuthenticatedUser($logged_in_user = false)
    {
        if (empty($logged_in_user) || empty($logged_in_user->user_id)) {
            return false;
        }

        $this->load->model('user_model');
        $this_user_details = $this->user_model->getByID($logged_in_user->user_id);

        if (empty($this_user_details)) {
            $this->auth->destroy();
            $this->session->set_flashdata('error', 'Your session is invalid. Please login again.');
            redirect(base_url('users/login?r=' . urlencode(current_url())), 'refresh');
        }

        return $this_user_details;
    }

    protected function layout($view = false, $template = 'layout')
    {
        if ($template == 'json' && $this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            echo json_encode($this->data['json']);
            exit;
        } elseif ($template == 'NONE') {
            $this->load->view($view, $this->data);
        } else {
            $this->data['maincontent'] = $this->load->view($view, $this->data, true);
            $this->load->view($template, $this->data);
        }
    }
}

class User_controller extends MY_Controller
{

    protected $user;
    protected $permissions;

    public function __construct()
    {
        parent::__construct();
        $auth = new Auth();
        $logged_in_user = $auth->logged_in();
        if (!$logged_in_user) {
            redirect(base_url('users/login?r=' . urlencode(current_url())), 'refresh');
        }

        $this->load->library('autoload_lib_sellers');
        $this_user_details = $this->validateAuthenticatedUser($logged_in_user);

        if ($this_user_details->user_type == 'telecaller')
            redirect(base_url('caller'), 'refresh');


        if ($this_user_details->parent_id == '0') {
            $logged_in_user->account_id = $logged_in_user->user_id;
            $this_user_details->account_id = $logged_in_user->user_id;
            $this_user_details->permissions = $this->config->item('permissions');
            $this->permissions = $this_user_details->permissions;
        } else {
            $logged_in_user->account_id = $this_user_details->parent_id;
            $this_user_details->account_id = $this_user_details->parent_id;
            $this_user_details->permissions = explode(',', $this_user_details->permissions);
            $this->permissions = $this_user_details->permissions;
        }

        $this->user = $logged_in_user;


        $this->data['user_details'] = $this_user_details;

        //check if user custom menu
        $side_menu = array();
        $side_menu = apply_filters('side_menu.menu_filters', $side_menu, $this->user->account_id);
        $this->data['side_menu_custom'] = $side_menu;
    }



    function userHasAccess($method = false)
    {
        if (!$method || !in_array($method, $this->permissions)) {
            $this->session->set_flashdata('error', 'No permission to access this page');
            redirect(base_url('dash'));
        }
    }
}

class Admin_controller extends MY_Controller
{

    protected $user;
    protected $permissions;
    protected $access_level;

    public function __construct()
    {
        parent::__construct();
        if (!$this->user = $this->auth->logged_in()) {
            redirect(base_url('users/login?r=' . urlencode(current_url())), 'refresh');
        }


        $auth = new Auth();
        $logged_in_user = $auth->logged_in();
        if (!$logged_in_user) {
            redirect(base_url('users/login?r=' . urlencode(current_url())), 'refresh');
        }

        $this->load->library('admin/user_lib');
        $this_user_details = $this->validateAuthenticatedUser($logged_in_user);

        if ($this_user_details->user_type == 'telecaller')
            redirect(base_url('caller'), 'refresh');

        if ($this_user_details->is_admin != '1') {
            redirect(base_url('users/login'), 'refresh');
        }


        $this_user_details->permissions = explode(',', $this_user_details->admin_permissions);
        $this->permissions = $this_user_details->permissions;
        $this->access_level = $this_user_details->admin_permission_level;

        $this->restricted_permissions = false;

        if ($this->access_level == 'restricted')
            $this->restricted_permissions = $this_user_details->id;



        $this->data['user_details'] = $this_user_details;

        $this->data['page_title'] = 'deltagloabal Admin';
    }

    function userHasAccess($method = false)
    {
        if (!$method || !in_array($method, $this->permissions)) {
            $this->session->set_flashdata('error', 'No permission to access this page');
            redirect(base_url('admin/dash'));
        }
    }

    function canAccess($method = false)
    {
        if (!$method || !in_array($method, $this->permissions)) {
            return false;
        }
        return true;
    }

    protected function layout($view = false, $template = 'admin/layout')
    {
        if ($template == 'json' && $this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            echo json_encode($this->data['json']);
            exit;
        } elseif ($template == 'NONE') {
            $this->load->view('admin/' . $view, $this->data);
        } else {
            $this->data['maincontent'] = $this->load->view('admin/' . $view, $this->data, true);
            $this->load->view($template, $this->data);
        }
    }
}

class Caller_controller extends MY_Controller
{

    protected $user;
    protected $permissions;
    protected $parent_seller;
    protected $caller_rules;

    public function __construct()
    {
        parent::__construct();
        $auth = new Auth();
        $logged_in_user = $auth->logged_in();
        if (!$logged_in_user) {
            redirect(base_url('users/login?r=' . urlencode(current_url())), 'refresh');
        }

        $this_user_details = $this->validateAuthenticatedUser($logged_in_user);

        //redirect user if not agent
        if ($this_user_details->user_type != 'telecaller')
            redirect(base_url('dash'), 'refresh');

        $this->load->library('telecaller_lib');

        $rules = $this->telecaller_lib->getByUserID($this_user_details->id);

        if (empty($rules))
            die('Access unassigned. Please contact support.');

        $json_rules = json_decode($rules->rules);

        $this->caller_rules = $json_rules;

        if (!empty($json_rules->permissions)) {
            $this->permissions = $json_rules->permissions;
            $this_user_details->permissions = $json_rules->permissions;
            $this_user_details->rules = $rules;
        }

        //check if user is admin or seller user
        if ($this_user_details->is_admin == '1') {
            //admin telecaller
            if (!empty($json_rules->seller))
                $this->parent_seller = $json_rules->seller;
        } else {
            //seller user
            $this->parent_seller = $this_user_details->parent_id;
        }

        $this_user_details->parent_seller = $this->parent_seller;


        $this->user = $logged_in_user;

        $this->data['user_details'] = $this_user_details;
    }

    function userHasAccess($method = false)
    {
        if (!$method || !in_array($method, $this->permissions)) {
            $this->session->set_flashdata('error', 'No permission to access this page');
            redirect(base_url('caller/dash'));
        }
    }

    protected function layout($view = false, $template = 'layout')
    {
        if ($template == 'json' && $this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            echo json_encode($this->data['json']);
            exit;
        } elseif ($template == 'NONE') {
            $this->load->view($view, $this->data);
        } else {
            $this->data['maincontent'] = $this->load->view($view, $this->data, true);
            $this->load->view($template, $this->data);
        }
    }
}

class Front_Controller extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data['page_title'] = 'Front Controller';
        $this->user = $this->auth->logged_in();

        if (!empty($this->user->user_id) && $this->uri->uri_string() != 'users/logout') {
            redirect('dash', 'refresh');
        }
    }
}
