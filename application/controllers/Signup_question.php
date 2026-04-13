<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Signup_question extends User_controller {

    public function __construct() {
        parent::__construct();
    }
    function index()
    {
       
        $shipping_partner = $this->input->post('shipping_partner');
        if (!empty($shipping_partner)) {
            $shipping_partner = implode(",\n", $shipping_partner);
        }

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'shipping_volume',
                'label' => 'Shipping Volume',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'industry_type',
                'label' => 'Industry Type',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'shipping_partner[]',
                'label' => 'Shipping Partner',
                'rules' => 'required'
            )

        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {

            $save_data = array(
                'shipping_volume' => $this->input->post('shipping_volume'),
                'industry_type' => $this->input->post('industry_type'),
                'shipping_partner' => $shipping_partner,
                'last_login_time' => time(),
            );
            
            $update_id =   $this->user_lib->update($this->user->account_id, $save_data);
               do_action('users.signup',$this->user->account_id);
            
            if ($update_id == true) {
                redirect('success', true);
            }
        } else {
          
            $this->layout('user/question', 'question_layout');
        }
    }

   
   
}
