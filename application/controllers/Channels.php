<?php

use Mpdf\Tag\Em;

defined('BASEPATH') or exit('No direct script access allowed');

class Channels extends User_controller
{
    private $allowed_image_extension;
	private $allowed_document_extension;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('channels_lib');
        $this->userHasAccess('settings');
        $this->load->library("s3");
		$this->allowed_image_extension = array('jpg', 'jpeg', 'png');
		$this->allowed_document_extension = array('pdf');
    }

    public function index()
    {
        $channels = $this->channels_lib->getChannelsByUserID($this->user->account_id);
        $this->data['channels'] = $channels;
        $this->layout('channels/index');
    }

    function add($channel = false)
    {
        switch ($channel) {
            case 'shopify':
                $this->add_shopify();
                break;
                
            default:

            
            $this->layout('channels/add');
        }
    }




    function edit($id = false)
    {
        if (!$id)
            redirect('channels', true);
        $channel = $this->channels_lib->getByID($id);
        if (empty($channel) || $channel->user_id != $this->user->account_id) {
            $this->session->set_flashdata('error', 'Invalid Request');
            redirect('channels', true);
        }
        //echo "==========".$channel->channel; amazon
        switch ($channel->channel) {
            case 'shopify':
                $this->add_shopify($id);
                break;
                        
            default:
                redirect('channels', true);
        }
    }

    function delete($channel_id = false)
    {

        if (!$channel_id)
            return false;

        $channel = $this->channels_lib->getByID($channel_id);

        if (empty($channel) || $channel->user_id != $this->user->account_id) {
            $this->session->set_flashdata('error', 'Invalid Request');
            redirect('channels', true);
        }

        $this->channels_lib->delete($channel_id);

        $this->session->set_flashdata('success', 'Channel Deleted');
        redirect('channels', true);
    }


   

    private function add_shopify($id = false)
    {

        if ($id) {
            $channel = $this->channels_lib->getByID($id);
            $this->data['channel'] = $channel;
        }

        $brand_logo=  "";
       
        

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'channel_name',
                'label' => 'Channel Name',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'host',
                'label' => 'Store URL',
                //'rules' => 'trim|required|min_length[2]|max_length[200]|callback_validate_shopify_url'
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'api_key',
                'label' => 'API Key',
                'rules' => 'trim|required|min_length[2]|max_length[200]|alpha_dash'
            ),
            array(
                'field' => 'api_password',
                'label' => 'Admin API Access token',
                'rules' => 'trim|required|min_length[2]|max_length[200]|alpha_dash|differs[api_key]'
            ),
            array(
                'field' => 'shared_secret',
                'label' => 'API secret key',
                'rules' => 'trim|required|min_length[2]|max_length[200]|alpha_dash'
            ),
            array(
                'field' => 'auto_fulfill',
                'label' => 'Fulfill Orders',
                'rules' => 'trim'
            ),
            array(
                'field' => 'auto_update_status',
                'label' => 'Auto Update Orders Status',
                'rules' => 'trim'
            ),
            array(
                'field' => 'auto_cancel',
                'label' => 'Cancel Orders',
                'rules' => 'trim'
            ),
            array(
                'field' => 'auto_cod_paid',
                'label' => 'Mark as Paid',
                'rules' => 'trim'
            ),
        );

        if (!empty($channel) && $channel->channel == 'shopify_oneclick') {
            $config = array(
                array(
                    'field' => 'channel_name',
                    'label' => 'Channel Name',
                    'rules' => 'trim|required|min_length[2]|max_length[200]'
                ),
                array(
                    'field' => 'auto_fulfill',
                    'label' => 'Fulfill Orders',
                    'rules' => 'trim'
                ),
                array(
                    'field' => 'auto_update_status',
                    'label' => 'Auto Update Orders Status',
                    'rules' => 'trim'
                ),
                array(
                    'field' => 'auto_cancel',
                    'label' => 'Cancel Orders',
                    'rules' => 'trim'
                ),
                array(
                    'field' => 'auto_cod_paid',
                    'label' => 'Mark as Paid',
                    'rules' => 'trim'
                ),
            );
        }

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {

            if (!empty($channel) && $channel->channel == 'shopify_oneclick') {
                $save = array(
                    'user_id' => $this->user->account_id,
                    'channel' => 'shopify_oneclick',
                    'channel_name' => $this->input->post('channel_name'),
                    'auto_fulfill' => ($this->input->post('auto_fulfill') >= '1') ? $this->input->post('auto_fulfill') : '0',
                    'auto_cancel' => ($this->input->post('auto_cancel') == '1') ? '1' : '0',
                    'auto_update_status' => ($this->input->post('auto_update_status') == '1') ? '1' : '0',
                    'auto_cod_paid' => ($this->input->post('auto_cod_paid') == '1') ? '1' : '0',
                );
            } else {
                $save = array(
                    'user_id' => $this->user->account_id,
                    'channel' => 'shopify',
                    'channel_name' => $this->input->post('channel_name'),
                    'api_field_1' => $this->format_shopify_url($this->input->post('host')),
                    'api_field_2' => $this->input->post('api_key'),
                    'api_field_3' => $this->input->post('api_password'),
                    'api_field_4' => $this->input->post('shared_secret'),
                    'auto_fulfill' => ($this->input->post('auto_fulfill') >= '1') ? $this->input->post('auto_fulfill') : '0',
                    'auto_cancel' => ($this->input->post('auto_cancel') == '1') ? '1' : '0',
                    'auto_update_status' => ($this->input->post('auto_update_status') == '1') ? '1' : '0',
                    'auto_cod_paid' => ($this->input->post('auto_cod_paid') == '1') ? '1' : '0',
                );
            }
            if (!empty($_FILES['brand_logo']['name'])) {
                if ($_FILES["brand_logo"]["size"] > '512000') {
                    $this->session->set_flashdata('error', 'File is to large.');
                    //$this->layout('channels/add_shopify');
                  }
                  else 
                  {
                     $brand_logo = $this->uploadFile('brand_logo', 'channel_brand_logo', true);

                     if (!empty($brand_logo)) {
                        $save['brand_logo'] = $brand_logo;
                        if ($id) {
                            $this->channels_lib->update($id, $save);
                            do_action('channel.update', $id);
                            $this->session->set_flashdata('success', 'Channel Updated Successfully');
                        } else {
                            $new_id = $this->channels_lib->create($save);
                            do_action('channel.create', $new_id);
                            $this->session->set_flashdata('success', 'Channel Created Successfully');
                        }
        
                        redirect('channels', true);
                    }
                  }
            }
            else
            {
                $save['brand_logo'] = $brand_logo;
                if ($id) {
                    $this->channels_lib->update($id, $save);
                    do_action('channel.update', $id);
                    $this->session->set_flashdata('success', 'Channel Updated Successfully');
                } else {
                    $new_id = $this->channels_lib->create($save);
                    do_action('channel.create', $new_id);
                    $this->session->set_flashdata('success', 'Channel Created Successfully');
                }
   
                redirect('channels', true);
            }


           
        } else {
            $this->data['error'] = validation_errors();
        }
        
        if (!empty($_FILES['brand_logo']['name'])) {
            if ($_FILES["brand_logo"]["size"] > '512000') {
                $this->session->set_flashdata('error', 'File is to large.');
                $this->data['error']='File is to large.';
              }
            }

        $this->layout('channels/add_shopify');
    }

    private function add_woocommerce($id = false)
    {

        if ($id) {
            $channel = $this->channels_lib->getByID($id);
            $this->data['channel'] = $channel;
        }

        $brand_logo=  "";
       

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'channel_name',
                'label' => 'Channel Name',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'host',
                'label' => 'Store URL',
                'rules' => 'trim|required|min_length[2]|max_length[200]|callback_validate_woocommerce_url'
            ),
            array(
                'field' => 'api_key',
                'label' => 'Consumer Key',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'api_password',
                'label' => 'Consumer Secret',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'status_to_fetch[]',
                'label' => 'Order Status to Fetch',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'push_status[]',
                'label' => 'Fulfillment Order Status',
                'rules' => 'trim'
            ),
            array(
                'field' => 'cod_titles',
                'label' => 'COD Payment Titles',
                'rules' => 'trim|required|min_length[2]|max_length[1000]'
            ),
            array(
                'field' => 'prepaid_separator',
                'label' => 'Prepaid Payment Separator',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'prepaid_titles',
                'label' => 'Prepaid Payment Titles',
                'rules' => 'trim|required|min_length[2]|max_length[1000]'
            ),
            array(
                'field' => 'auto_fulfill',
                'label' => 'Fulfill Orders',
                'rules' => 'trim'
            ),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $save = array(
                'user_id' => $this->user->account_id,
                'channel' => 'woocommerce',
                'channel_name' => $this->input->post('channel_name'),
                'api_field_1' => $this->input->post('host'),
                'api_field_2' => $this->input->post('api_key'),
                'api_field_3' => $this->input->post('api_password'),
                'status_to_fetch' => strtolower(implode(',', $this->input->post('status_to_fetch'))),
                'push_status_multiple' => json_encode($this->input->post('push_status')),
                'cod_titles' => strtolower(implode(',', array_map('trim', explode(',', $this->input->post('cod_titles'))))),
                'prepaid_titles' => strtolower(implode(',', array_map('trim', explode(',', $this->input->post('prepaid_titles'))))),
                'prepaid_separator' => strtolower($this->input->post('prepaid_separator')),
                'auto_fulfill' => ($this->input->post('auto_fulfill') == '1') ? '1' : '0',
            );

            if (!empty($_FILES['brand_logo']['name'])) {

                if ($_FILES["brand_logo"]["size"] > '512000') {
                    $this->session->set_flashdata('error', 'File is to large.');
                  }
                  else 
                  {
                     $brand_logo = $this->uploadFile('brand_logo', 'channel_brand_logo', true);
                     if (!empty($brand_logo)) {
                        $save['brand_logo'] = $brand_logo;

                        if ($id) {
                            $this->channels_lib->update($id, $save);
                            do_action('channel.update', $id);
                            $this->session->set_flashdata('success', 'Channel Updated Successfully');
                        } else {
                            $new_id = $this->channels_lib->create($save);
                            do_action('channel.create', $new_id);
                            $this->session->set_flashdata('success', 'Channel Created Successfully');
                        }
            
                        redirect('channels', true);
                    }
                  }
            }
            else
            {
                if ($id) {
                    $this->channels_lib->update($id, $save);
                    do_action('channel.update', $id);
                    $this->session->set_flashdata('success', 'Channel Updated Successfully');
                } else {
                    $new_id = $this->channels_lib->create($save);
                    do_action('channel.create', $new_id);
                    $this->session->set_flashdata('success', 'Channel Created Successfully');
                }
    
                redirect('channels', true);
            }



         
        } else {
            $this->data['error'] = validation_errors();
        }
        if (!empty($_FILES['brand_logo']['name'])) {
            if ($_FILES["brand_logo"]["size"] > '512000') {
                $this->session->set_flashdata('error', 'File is to large.');
                $this->data['error']='File is to large.';
              }
            }
        $this->layout('channels/add_woocommerce');
    }

    private function add_magento2($id = false)
    {

        if ($id) {
            $channel = $this->channels_lib->getByID($id);
            $this->data['channel'] = $channel;
        }
        $brand_logo=  "";
       
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'channel_name',
                'label' => 'Channel Name',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'host',
                'label' => 'Store URL',
                'rules' => 'trim|required|min_length[2]|max_length[200]|callback_magento_validate_url'
            ),
            array(
                'field' => 'consumer_api_key',
                'label' => 'Username',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'consumer_api_secret',
                'label' => 'Password',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'status_to_fetch[]',
                'label' => 'Order Status to Fetch',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'cod_titles',
                'label' => 'COD Payment Titles',
                'rules' => 'trim|required|min_length[2]|max_length[1000]'
            ),
            array(
                'field' => 'prepaid_separator',
                'label' => 'Prepaid Payment Separator',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'prepaid_titles',
                'label' => 'Prepaid Payment Titles',
                'rules' => 'trim|required|min_length[2]|max_length[1000]'
            ),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $save = array(
                'user_id' => $this->user->account_id,
                'channel' => 'magento2',
                'channel_name' => $this->input->post('channel_name'),
                'api_field_1' => $this->input->post('host'),
                'api_field_2' => $this->input->post('consumer_api_key'),
                'api_field_3' => $this->input->post('consumer_api_secret'),
                'status_to_fetch' => strtolower(implode(',', $this->input->post('status_to_fetch'))),
                'cod_titles' => strtolower(implode(',', array_map('trim', explode(',', $this->input->post('cod_titles'))))),
                'prepaid_titles' => strtolower(implode(',', array_map('trim', explode(',', $this->input->post('prepaid_titles'))))),
                'prepaid_separator' => strtolower($this->input->post('prepaid_separator')),
            );

            if (!empty($_FILES['brand_logo']['name'])) {

                if ($_FILES["brand_logo"]["size"] > '512000') {
                    $this->session->set_flashdata('error', 'File is to large.');
                  }
                  else 
                  {
                     $brand_logo = $this->uploadFile('brand_logo', 'channel_brand_logo', true);
                     if (!empty($brand_logo)) {
                        $save['brand_logo'] = $brand_logo;
                        if ($id) {
                            $this->channels_lib->update($id, $save);
                            do_action('channel.update', $id);
                            $this->session->set_flashdata('success', 'Channel Updated Successfully');
                        } else {
                            $new_id = $this->channels_lib->create($save);
                            do_action('channel.create', $new_id);
                            $this->session->set_flashdata('success', 'Channel Created Successfully');
                        }
            
                        redirect('channels', true);
                    }
                  }
            }
            else
            {
                if ($id) {
                    $this->channels_lib->update($id, $save);
                    do_action('channel.update', $id);
                    $this->session->set_flashdata('success', 'Channel Updated Successfully');
                } else {
                    $new_id = $this->channels_lib->create($save);
                    do_action('channel.create', $new_id);
                    $this->session->set_flashdata('success', 'Channel Created Successfully');
                }
    
                redirect('channels', true);
            }


            
        } else {
            $this->data['error'] = validation_errors();
        }
        if (!empty($_FILES['brand_logo']['name'])) {
            if ($_FILES["brand_logo"]["size"] > '512000') {
                $this->session->set_flashdata('error', 'File is to large.');
                $this->data['error']='File is to large.';
              }
            }
        $this->layout('channels/add_magento2');
    }

     private function add_magento($id = false)
    {
        $brand_logo=  "";
        if ($id) {
            $channel = $this->channels_lib->getByID($id);
            $this->data['channel'] = $channel;
        }
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'channel_name',
                'label' => 'Channel Name',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'host',
                'label' => 'Store URL',
                'rules' => 'trim|required|min_length[2]|max_length[200]|callback_magento_validate_url'
            ),
            array(
                'field' => 'consumer_api_key',
                'label' => 'Username',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'consumer_api_secret',
                'label' => 'Password',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'status_to_fetch[]',
                'label' => 'Order Status to Fetch',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'cod_titles',
                'label' => 'COD Payment Titles',
                'rules' => 'trim|required|min_length[2]|max_length[1000]'
            ),
            array(
                'field' => 'prepaid_separator',
                'label' => 'Prepaid Payment Separator',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'prepaid_titles',
                'label' => 'Prepaid Payment Titles',
                'rules' => 'trim|required|min_length[2]|max_length[1000]'
            ),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $save = array(
                'user_id' => $this->user->account_id,
                'channel' => 'magento',
                'channel_name' => $this->input->post('channel_name'),
                'api_field_1' => $this->input->post('host'),
                'api_field_2' => $this->input->post('consumer_api_key'),
                'api_field_3' => $this->input->post('consumer_api_secret'),
                'status_to_fetch' => strtolower(implode(',', $this->input->post('status_to_fetch'))),
                'cod_titles' => strtolower(implode(',', array_map('trim', explode(',', $this->input->post('cod_titles'))))),
                'prepaid_titles' => strtolower(implode(',', array_map('trim', explode(',', $this->input->post('prepaid_titles'))))),
                'prepaid_separator' => strtolower($this->input->post('prepaid_separator')),
            );


            if (!empty($_FILES['brand_logo']['name'])) {

                if ($_FILES["brand_logo"]["size"] > '512000') {
                    $this->session->set_flashdata('error', 'File is to large.');
                  }
                  else 
                  {
                     $brand_logo = $this->uploadFile('brand_logo', 'channel_brand_logo', true);
                     if (!empty($brand_logo)) {
                        $save['brand_logo'] = $brand_logo;

                        if ($id) {
                            $this->channels_lib->update($id, $save);
                            do_action('channel.update', $id);
                            $this->session->set_flashdata('success', 'Channel Updated Successfully');
                        } else {
                            $new_id = $this->channels_lib->create($save);
                            do_action('channel.create', $new_id);
                            $this->session->set_flashdata('success', 'Channel Created Successfully');
                        }
            
                        redirect('channels', true);
                    }
                  }
            }
            else
            {
                
                if ($id) {
                    $this->channels_lib->update($id, $save);
                    do_action('channel.update', $id);
                    $this->session->set_flashdata('success', 'Channel Updated Successfully');
                } else {
                    $new_id = $this->channels_lib->create($save);
                    do_action('channel.create', $new_id);
                    $this->session->set_flashdata('success', 'Channel Created Successfully');
                }
    
                redirect('channels', true);
            }
            
            //****************originl code*************************/

           

           // redirect('channels', true);
        } else {
            $this->data['error'] = validation_errors();
        }

        if (!empty($_FILES['brand_logo']['name'])) {
            if ($_FILES["brand_logo"]["size"] > '512000') {
                $this->session->set_flashdata('error', 'File is to large.');
                $this->data['error']='File is to large.';
              }
            }

        $this->layout('channels/add_magento');
    }


    private function bigcommerse($id = false)
    {

        if ($id) {
            $channel = $this->channels_lib->getByID($id);
            $this->data['channel'] = $channel;
        }
        $brand_logo=  "";

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'channel_name',
                'label' => 'Channel Name',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'host',
                'label' => 'Store URL',
                'rules' => 'trim|required|min_length[2]|max_length[200]|callback_magento_validate_url'
            ),
            array(
                'field' => 'consumer_api_key',
                'label' => 'Username',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'consumer_api_secret',
                'label' => 'Password',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'status_to_fetch[]',
                'label' => 'Order Status to Fetch',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'cod_titles',
                'label' => 'COD Payment Titles',
                'rules' => 'trim|required|min_length[2]|max_length[1000]'
            ),
            array(
                'field' => 'prepaid_separator',
                'label' => 'Prepaid Payment Separator',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'prepaid_titles',
                'label' => 'Prepaid Payment Titles',
                'rules' => 'trim|required|min_length[2]|max_length[1000]'
            ),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $save = array(
                'user_id' => $this->user->account_id,
                'channel' => 'magento2',
                'channel_name' => $this->input->post('channel_name'),
                'api_field_1' => $this->input->post('host'),
                'api_field_2' => $this->input->post('consumer_api_key'),
                'api_field_3' => $this->input->post('consumer_api_secret'),
                'status_to_fetch' => strtolower(implode(',', $this->input->post('status_to_fetch'))),
                'cod_titles' => strtolower(implode(',', array_map('trim', explode(',', $this->input->post('cod_titles'))))),
                'prepaid_titles' => strtolower(implode(',', array_map('trim', explode(',', $this->input->post('prepaid_titles'))))),
                'prepaid_separator' => strtolower($this->input->post('prepaid_separator')),
            );



          if (!empty($_FILES['brand_logo']['name'])) {

             if ($_FILES["brand_logo"]["size"] > '512000') {
               $this->session->set_flashdata('error', 'File is to large.');
              }
              else 
               {
                  $brand_logo = $this->uploadFile('brand_logo', 'channel_brand_logo', true);
                  if (!empty($brand_logo)) {
                   $save['brand_logo'] = $brand_logo;

                    if ($id) {
                        $this->channels_lib->update($id, $save);
                        do_action('channel.update', $id);
                        $this->session->set_flashdata('success', 'Channel Updated Successfully');
                     } else {
                       $new_id = $this->channels_lib->create($save);
                       do_action('channel.create', $new_id);
                       $this->session->set_flashdata('success', 'Channel Created Successfully');
                     }

                    redirect('channels', true);
                  }
                }
              }
             else
               {

                  if ($id) {
                      $this->channels_lib->update($id, $save);
                      do_action('channel.update', $id);
                      $this->session->set_flashdata('success', 'Channel Updated Successfully');
                } else {
                     $new_id = $this->channels_lib->create($save);
                     do_action('channel.create', $new_id);
                     $this->session->set_flashdata('success', 'Channel Created Successfully');
                 }

                    redirect('channels', true);
             }

        } else {
            $this->data['error'] = validation_errors();
        }

        if (!empty($_FILES['brand_logo']['name'])) {
            if ($_FILES["brand_logo"]["size"] > '512000') {
                $this->session->set_flashdata('error', 'File is to large.');
                $this->data['error']='File is to large.';
              }
            }


        $this->layout('channels/bigcommerce');
    }

    private function add_storehippo($id = false)
    {

        if ($id) {
            $channel = $this->channels_lib->getByID($id);
            $this->data['channel'] = $channel;
        }

        $brand_logo=  "";
       
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'channel_name',
                'label' => 'Channel Name',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'host',
                'label' => 'Store Name',
                'rules' => 'trim|required|min_length[2]|max_length[200]|alpha_dash'
            ),
            array(
                'field' => 'api_key',
                'label' => 'Access Key',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $save = array(
                'user_id' => $this->user->account_id,
                'channel' => 'storehippo',
                'channel_name' => $this->input->post('channel_name'),
                'api_field_1' => $this->input->post('host'),
                'api_field_2' => $this->input->post('api_key'),
            );
            if (!empty($_FILES['brand_logo']['name'])) {

                if ($_FILES["brand_logo"]["size"] > '512000') {
                    $this->session->set_flashdata('error', 'File is to large.');
                  }
                  else 
                  {
                     $brand_logo = $this->uploadFile('brand_logo', 'channel_brand_logo', true);
                     if (!empty($brand_logo)) {
                        $save['brand_logo'] = $brand_logo;

                        if ($id) {
                            $this->channels_lib->update($id, $save);
                            do_action('channel.update', $id);
                            $this->session->set_flashdata('success', 'Channel Updated Successfully');
                        } else {
                            $new_id = $this->channels_lib->create($save);
                            do_action('channel.create', $new_id);
                            $this->session->set_flashdata('success', 'Channel Created Successfully');
                        }
            
                        redirect('channels', true);
                    }
                  }
            }
            else
            {
                
                if ($id) {
                    $this->channels_lib->update($id, $save);
                    do_action('channel.update', $id);
                    $this->session->set_flashdata('success', 'Channel Updated Successfully');
                } else {
                    $new_id = $this->channels_lib->create($save);
                    do_action('channel.create', $new_id);
                    $this->session->set_flashdata('success', 'Channel Created Successfully');
                }
    
                redirect('channels', true);
            }


            
        } else {
            $this->data['error'] = validation_errors();
        }
        if (!empty($_FILES['brand_logo']['name'])) {
            if ($_FILES["brand_logo"]["size"] > '512000') {
                $this->session->set_flashdata('error', 'File is to large.');
                $this->data['error']='File is to large.';
              }
            }
        $this->layout('channels/add_storehippo');
    }

    private function add_kartrocket($id = false)
    {

        if ($id) {
            $channel = $this->channels_lib->getByID($id);
            $this->data['channel'] = $channel;
        }

        $brand_logo=  "";
       

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'channel_name',
                'label' => 'Channel Name',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'host',
                'label' => 'Store URL',
                'rules' => 'trim|required|min_length[2]|max_length[200]|callback_validate_woocommerce_url'
            ),
            array(
                'field' => 'api_key',
                'label' => 'API Key',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'auto_fulfill',
                'label' => 'Fulfill Orders',
                'rules' => 'trim'
            ),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $save = array(
                'user_id' => $this->user->account_id,
                'channel' => 'kartrocket',
                'channel_name' => $this->input->post('channel_name'),
                'api_field_1' => $this->input->post('host'),
                'api_field_2' => $this->input->post('api_key'),
                'auto_fulfill' => ($this->input->post('auto_fulfill') == '1') ? '1' : '0',
            );

            if (!empty($_FILES['brand_logo']['name'])) {

                if ($_FILES["brand_logo"]["size"] > '512000') {
                    $this->session->set_flashdata('error', 'File is to large.');
                  }
                  else 
                  {
                     $brand_logo = $this->uploadFile('brand_logo', 'channel_brand_logo', true);
                     if (!empty($brand_logo)) {
                        $save['brand_logo'] = $brand_logo;

                        if ($id) {
                            $this->channels_lib->update($id, $save);
                            do_action('channel.update', $id);
                            $this->session->set_flashdata('success', 'Channel Updated Successfully');
                        } else {
                            $new_id = $this->channels_lib->create($save);
                            do_action('channel.create', $new_id);
                            $this->session->set_flashdata('success', 'Channel Created Successfully');
                        }
            
                        redirect('channels', true);
                    }
                  }
            }
            else
            {
                if ($id) {
                    $this->channels_lib->update($id, $save);
                    do_action('channel.update', $id);
                    $this->session->set_flashdata('success', 'Channel Updated Successfully');
                } else {
                    $new_id = $this->channels_lib->create($save);
                    do_action('channel.create', $new_id);
                    $this->session->set_flashdata('success', 'Channel Created Successfully');
                }
    
                redirect('channels', true);
            }


          
        } else {
            $this->data['error'] = validation_errors();
        }
        if (!empty($_FILES['brand_logo']['name'])) {
            if ($_FILES["brand_logo"]["size"] > '512000') {
                $this->session->set_flashdata('error', 'File is to large.');
                $this->data['error']='File is to large.';
              }
            }
        $this->layout('channels/add_kartrocket');
    }

    public function validate_woocommerce_url($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
            $this->form_validation->set_message('validate_woocommerce_url', 'Please enter a valid URL in format http://yourstore.com or https://yourstore.com');
            return FALSE;
        } else {
            return true;
        }
    }

    public function validate_shopify_url($url)
    {
        $this->form_validation->set_message('validate_shopify_url', 'Please enter a valid store URL in format your store.shopify.com');

        if (empty($url))
            return false;

        $url = $this->format_shopify_url($url);

        if (!preg_match("/\.myshopify\.com/", $url))
            return false;


        return true;
    }

    private function format_shopify_url($url = false)
    {
        if (empty($url))
            return false;

        if (!empty(parse_url($url, PHP_URL_HOST)))
            $url = parse_url($url, PHP_URL_HOST);
        else
            $url = parse_url($url, PHP_URL_PATH);

        $url = preg_replace('/https?:\/\/(www\.)?/', '', rtrim($url, '/'));
        $url = str_replace('www.', '', $url);

        return $url;
    }
    private function add_kwikfunnels($id = false)
    {

        if ($id) {
            $channel = $this->channels_lib->getByID($id);
            $this->data['channel'] = $channel;
        }
        $brand_logo=  "";
       
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'channel_name',
                'label' => 'Channel Name',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'host',
                'label' => 'Store URL',
                'rules' => 'trim|required|min_length[2]|max_length[200]|callback_validate_url'
            ),
            array(
                'field' => 'api_key',
                'label' => 'Consumer Key',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'api_password',
                'label' => 'Consumer Secret',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            )
          
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $save = array(
                'user_id' => $this->user->account_id,
                'channel' => 'kwikfunnels',
                'channel_name' => $this->input->post('channel_name'),
                'api_field_1' => $this->input->post('host'),
                'api_field_2' => $this->input->post('api_key'),
                'api_field_3' => $this->input->post('api_password')
            );
            if (!empty($_FILES['brand_logo']['name'])) {

                if ($_FILES["brand_logo"]["size"] > '512000') {
                    $this->session->set_flashdata('error', 'File is to large.');
                  }
                  else 
                  {
                     $brand_logo = $this->uploadFile('brand_logo', 'channel_brand_logo', true);
                     if (!empty($brand_logo)) {
                        $save['brand_logo'] = $brand_logo;

                        if ($id) {
                            $this->channels_lib->update($id, $save);
                            do_action('channel.update', $id);
                            $this->session->set_flashdata('success', 'Channel Updated Successfully');
                            redirect('channels/edit', true);
                        } else {
                            $new_id = $this->channels_lib->create($save);
                           do_action('channel.create', $new_id);
                            $this->session->set_flashdata('success', 'Channel Created Successfully');
                            redirect('channels/edit/'.$new_id.'?status=success', true);
                        }
                    }
                  }
            }
            else
            {
                if ($id) {
                    $this->channels_lib->update($id, $save);
                    do_action('channel.update', $id);
                    $this->session->set_flashdata('success', 'Channel Updated Successfully');
                    redirect('channels/edit', true);
                } else {
                    $new_id = $this->channels_lib->create($save);
                   do_action('channel.create', $new_id);
                    $this->session->set_flashdata('success', 'Channel Created Successfully');
                    redirect('channels/edit/'.$new_id.'?status=success', true);
                }
            }

           
            
        } else {
            $this->data['error'] = validation_errors();
        }

        if (!empty($_FILES['brand_logo']['name'])) {
            if ($_FILES["brand_logo"]["size"] > '512000') {
                $this->session->set_flashdata('error', 'File is to large.');
                $this->data['error']='File is to large.';
              }
            }

        $this->layout('channels/add_kwikfunnels');
    }

    private function add_amazon($id = false)
    {

        if ($id) {
            $channel = $this->channels_lib->getByID($id);
            $this->data['channel'] = $channel;
        }
        $brand_logo=  "";
       
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'channel_name',
                'label' => 'Channel Name',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            )
          
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            
            $save = array(
                'user_id' => $this->user->account_id,
                'channel' => 'amazon',
                'channel_name' => $this->input->post('channel_name')
            );

            if (!empty($_FILES['brand_logo']['name'])) {

                if ($_FILES["brand_logo"]["size"] > '512000') {
                    $this->session->set_flashdata('error', 'File is to large.');
                  }
                  else 
                  {
                     $brand_logo = $this->uploadFile('brand_logo', 'channel_brand_logo', true);
                     if (!empty($brand_logo)) {
                        $save['brand_logo'] = $brand_logo;

                        if ($id) {
                            $this->channels_lib->update($id, $save);
                            do_action('channel.update', $id);
                            $this->session->set_flashdata('success', 'Channel Updated Successfully');
                            redirect('channels/edit', true);
                        } else {
                            $new_id = $this->channels_lib->create($save);
                           do_action('channel.create', $new_id);
                            $this->session->set_flashdata('success', 'Channel Created Successfully');
                            redirect('channels/edit/'.$new_id.'?status=success', true);
                        }
                    }
                  }
            }
            else
            {
                if ($id) {
                    $this->channels_lib->update($id, $save);
                    do_action('channel.update', $id);
                    $this->session->set_flashdata('success', 'Channel Updated Successfully');
                    redirect('channels/edit', true);
                } else {
                    $new_id = $this->channels_lib->create($save);
                   do_action('channel.create', $new_id);
                    $this->session->set_flashdata('success', 'Channel Created Successfully');
                    redirect('channels/edit/'.$new_id.'?status=success', true);
                }
            }

            
            
            
        } else {
            $this->data['error'] = validation_errors();
        }

        if (!empty($_FILES['brand_logo']['name'])) {
            if ($_FILES["brand_logo"]["size"] > '512000') {
                $this->session->set_flashdata('error', 'File is to large.');
                $this->data['error']='File is to large.';
              }
            }

        $this->layout('channels/add_amazon');
    }
    public function validate_url($url)
    {
        if(filter_var($url, FILTER_VALIDATE_URL) === false){
        $this->form_validation->set_message('validate_url', 'Please enter a valid URL in format https://yourstore.kwikfunnels.com ');
        return false;
        } else {
        return true;
        }
    }

    public function magento_validate_url($url)
    {
        if(filter_var($url, FILTER_VALIDATE_URL) === false){
        $this->form_validation->set_message('validate_url', 'Please enter a valid URL in format https://yourstore.com ');
        return false;
        } else {
        return true;
        }
    }

    private function add_vinculum($id = false)
    {

        if ($id) {
            $channel = $this->channels_lib->getByID($id);
            $this->data['channel'] = $channel;
        }
        $brand_logo=  "";
        

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'channel_name',
                'label' => 'Channel Name',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'host',
                'label' => 'Store URL',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'api_key',
                'label' => 'API Key',
                'rules' => 'trim|required|min_length[2]|max_length[200]|alpha_dash'
            ),
            array(
                'field' => 'api_owner',
                'label' => 'API Owner',
                'rules' => 'trim|required|min_length[2]|max_length[200]|alpha_dash|differs[api_key]'
            )
        );


        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
                $couriers = $this->input->post('courier');
                
                $mappedCourier = array();
                if(!empty($couriers))
                {
                    foreach($couriers as $courier)
                    {
                        $mappedCourier[$courier['name']] = $courier['code'];
                    }
                }
                
                $save = array(
                    'user_id' => $this->user->account_id,
                    'channel' => 'vinculum',
                    'channel_name'=> $this->input->post('channel_name'),
                    'api_field_1' => $this->input->post('host'),
                    'api_field_2' => $this->input->post('api_key'),
                    'api_field_3' => $this->input->post('api_owner'),
                    'api_field_4' => $this->input->post('channel_code'),
                    'api_field_5' => json_encode($mappedCourier),
                );

                if (!empty($_FILES['brand_logo']['name'])) 
                {

                    if ($_FILES["brand_logo"]["size"] > '512000') {
                        $this->session->set_flashdata('error', 'File is to large.');
                      }
                      else 
                      {
                         $brand_logo = $this->uploadFile('brand_logo', 'channel_brand_logo', true);
                         if (!empty($brand_logo)) {
                            $save['brand_logo'] = $brand_logo;

                            if ($id) {
                                $this->channels_lib->update($id, $save);
                                do_action('channel.update', $id);
                                $this->session->set_flashdata('success', 'Channel Updated Successfully');
                            } else {
                                $new_id = $this->channels_lib->create($save);
                                do_action('channel.create', $new_id);
                                $this->session->set_flashdata('success', 'Channel Created Successfully');
                            }
                
                            redirect('channels', true);

                        }
                      }
                }
                else
                {
                    if ($id) {
                        $this->channels_lib->update($id, $save);
                        do_action('channel.update', $id);
                        $this->session->set_flashdata('success', 'Channel Updated Successfully');
                    } else {
                        $new_id = $this->channels_lib->create($save);
                        do_action('channel.create', $new_id);
                        $this->session->set_flashdata('success', 'Channel Created Successfully');
                    }
        
                    redirect('channels', true);
                }

    
         
        } else {
            $this->data['error'] = validation_errors();
        }
        
        if (!empty($_FILES['brand_logo']['name'])) {
            if ($_FILES["brand_logo"]["size"] > '512000') {
                $this->session->set_flashdata('error', 'File is to large.');
                $this->data['error']='File is to large.';
              }
            }

        $this->load->library('courier_lib');
        $this->data['couriers'] = $this->courier_lib->list_couriers();
        
        $this->layout('channels/add_vinculum');
    }


     private function add_easyecom($id = false)
    {


        if ($id) {
            $channel = $this->channels_lib->getByID($id);
            $this->load->library('easyecom_lib');
            // $existing_courier = $this->easyecom_lib->getByChannel($id);
            $this->data['channel'] = $channel;
            // $this->data['existing_courier'] = $existing_courier;
        }

        $brand_logo=  "";
      
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'channel_name',
                'label' => 'Channel Name',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            // array(
            //     'field' => 'host',
            //     'label' => 'Store URL',
            //     'rules' => 'trim|required|min_length[2]|max_length[200]'
            // ),
            array(
                'field' => 'email_id',
                'label' => 'Email id',
                'rules' => 'trim|required|min_length[2]|max_length[200]|valid_email'
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            )
        );


        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
          //  pr($_POST);EXIT;
                $couriers = $this->input->post('courier');
                
                $save = array(
                    'user_id' => $this->user->account_id,
                    'channel' => 'easyecom',
                    'channel_name'=> $this->input->post('channel_name'),
                    // 'api_field_1' => $this->input->post('host'),
                    'api_field_2' => $this->input->post('email_id'),
                    'api_field_3' => $this->input->post('password'),
                    'api_field_4' => $this->input->post('channel_code'),
                    'api_field_5' => $this->input->post('marketshipped_status'),
                    );

                    if (!empty($_FILES['brand_logo']['name'])) {

                        if ($_FILES["brand_logo"]["size"] > '512000') {
                            $this->session->set_flashdata('error', 'File is to large.');
                          }
                          else 
                          {
                             $brand_logo = $this->uploadFile('brand_logo', 'channel_brand_logo', true);
                             if (!empty($brand_logo)) {
                                $save['brand_logo'] = $brand_logo;

                                if ($id) {
                                    $this->channels_lib->update($id, $save);
                                    do_action('channel.update', $id);
                                   // $this->addCourierToEasyecom($couriers,$id);
                    
                                    $this->session->set_flashdata('success', 'Channel Updated Successfully');
                                } else {
                                    $new_id = $this->channels_lib->create($save);
                                    do_action('channel.create', $new_id);
                                    $couriers[] = "1~14394" ;  // hardcod for now
                                    $this->addCourierToEasyecom($couriers,$new_id);
                                    $this->session->set_flashdata('success', 'Channel Created Successfully');
                                }
                    
                                redirect('channels', true);
                            }
                          }
                    }
                    else
                    {
                        
                        if ($id) {
                            $this->channels_lib->update($id, $save);
                            do_action('channel.update', $id);
                           // $this->addCourierToEasyecom($couriers,$id);
            
                            $this->session->set_flashdata('success', 'Channel Updated Successfully');
                        } else {
                            $new_id = $this->channels_lib->create($save);
                            do_action('channel.create', $new_id);
                            $couriers[] = "1~14394" ;  // hardcod for now
                            $this->addCourierToEasyecom($couriers,$new_id);
                            $this->session->set_flashdata('success', 'Channel Created Successfully');
                        }
            
                        redirect('channels', true);
                    }

                  
            

            
        } else {
            $this->data['error'] = validation_errors();
        }
        
        $this->load->library('courier_lib');
      
        if (!empty($_FILES['brand_logo']['name'])) {
            if ($_FILES["brand_logo"]["size"] > '512000') {
                $this->session->set_flashdata('error', 'File is to large.');
                $this->data['error']='File is to large.';
              }
            }
        
        $this->layout('channels/add_easyecom');
    }

    private function add_bigcommerce($id = false)
    {
        if ($id) {
            $channel = $this->channels_lib->getByID($id);
            $this->data['channel'] = $channel;
        }
        $brand_logo=  "";

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'channel_name',
                'label' => 'Channel Name',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'host',
                'label' => 'Store URL',
                'rules' => 'trim|required|min_length[2]|max_length[100]'
            ),
            array(
                'field' => 'api_key',
                'label' => 'Consumer Key',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            array(
                'field' => 'api_password',
                'label' => 'Consumer Secret',
                'rules' => 'trim|required|min_length[2]|max_length[200]'
            ),
            // array(
            //     'field' => 'status_to_fetch[]',
            //     'label' => 'Order Status to Fetch',
            //     'rules' => 'trim|required|min_length[2]|max_length[200]'
            // ),
            // array(
            //     'field' => 'push_status[]',
            //     'label' => 'Fulfillment Order Status',
            //     'rules' => 'trim'
            // ),
            // array(
            //     'field' => 'cod_titles',
            //     'label' => 'COD Payment Titles',
            //     'rules' => 'trim|required|min_length[2]|max_length[1000]'
            // ),
            // array(
            //     'field' => 'prepaid_separator',
            //     'label' => 'Prepaid Payment Separator',
            //     'rules' => 'trim|required'
            // ),
            // array(
            //     'field' => 'prepaid_titles',
            //     'label' => 'Prepaid Payment Titles',
            //     'rules' => 'trim|required|min_length[2]|max_length[1000]'
            // ),
            array(
                'field' => 'auto_fulfill',
                'label' => 'Fulfill Orders',
                'rules' => 'trim'
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $save = array(
                'user_id' => $this->user->account_id,
                'channel' => 'bigcommerce',
                'channel_name' => $this->input->post('channel_name'),
                'api_field_1' => $this->input->post('host'),
                'api_field_2' => $this->input->post('api_key'),
                'api_field_3' => $this->input->post('api_password'),
               
                
            );

            


if (!empty($_FILES['brand_logo']['name'])) {

    if ($_FILES["brand_logo"]["size"] > '512000') { $this->session->set_flashdata('error', 'File is to large.');
      }
      else 
      {
         $brand_logo = $this->uploadFile('brand_logo', 'channel_brand_logo', true);
         if (!empty($brand_logo)) {
            $save['brand_logo'] = $brand_logo;
    
            if ($id) {
                    $this->channels_lib->update($id, $save);
                    do_action('channel.update', $id);
                    $this->session->set_flashdata('success', 'Channel Updated Successfully');
                } else {
                    $new_id = $this->channels_lib->create($save);
                    do_action('channel.create', $new_id);
                    $this->session->set_flashdata('success', 'Channel Created Successfully');
                }
    
                redirect('channels', true);
        }
      }
    }
    else
    {
    
        if ($id) {
                    $this->channels_lib->update($id, $save);
                    do_action('channel.update', $id);
                    $this->session->set_flashdata('success', 'Channel Updated Successfully');
                } else {
                    $new_id = $this->channels_lib->create($save);
                    do_action('channel.create', $new_id);
                    $this->session->set_flashdata('success', 'Channel Created Successfully');
                }
                redirect('channels', true);
    }
    
    
    
           
        } else {
            $this->data['error'] = validation_errors();
        }

        
if (!empty($_FILES['brand_logo']['name'])) {
    if ($_FILES["brand_logo"]["size"] > '512000') {
        $this->session->set_flashdata('error', 'File is to large.');
        $this->data['error']='File is to large.';
      }
    }
    
        $this->layout('channels/add_bigcommerce');
     }
    



    public function addCourierToEasyecom($couriers,$channel_id){
              if(!empty($couriers)){
             //   pr($couriers);exit;
           
            foreach($couriers as $courier){
                $mapping_details = explode("~",$courier);
                $courier_id = isset($mapping_details['0'])?$mapping_details['0']:"";
                $easy_ecom_id = isset($mapping_details['1'])?$mapping_details['1']:"";
                $save = array("user_id"=>$this->user->account_id,"easyecom_courier_id"=>$easy_ecom_id,"courier_id"=>$courier_id,"channel_id"=>$channel_id);
                $this->load->library('easyecom_lib');
                $map_id = $this->easyecom_lib->insert($save);
                if(!empty($map_id)){
                     //pr($map_id); echo "Sd";exit;

                   do_action('channel.add_courier_to_easyecom',$map_id); 
                }
               
                
               
            }
        }
    }
 
    public function amazonurl()
    {

        $this->load->library('channels/amazon');
        $amazonUrl = $this->amazon->getAmazonAppUrl();    
        if(!empty($amazonUrl)){
            return  redirect($amazonUrl, 'refresh');
        }
        redirect('channels', true);
         exit();
    }

    public function shopifyurl()
    {
        $this->load->library('install_lib');
        $url = $this->install_lib->getshopifyurl();    
        if(!empty($url)){
            return  redirect($url, 'refresh');
        }
        redirect('channels', true);
         exit();
    }

    private function uploadFile($variable_name = null, $folder_name = null, $image_only = false)
	{
		if ($variable_name == null || $folder_name == null) {
			return '';
		}
		$returnval = '';
		$extension = strtolower(pathinfo($_FILES[$variable_name]['name'], PATHINFO_EXTENSION));

		$new_name = time() . rand(1111, 9999) . '.' . ($extension);

		if (($image_only && in_array($extension, $this->allowed_image_extension)) || (!$image_only && (in_array($extension, $this->allowed_image_extension)))) {
			$config['file_name'] = $new_name;

			$fileTempName = $_FILES[$variable_name]['tmp_name'];
			$image_name = $new_name;

			$file_name = $this->s3->amazonS3Upload($image_name, $fileTempName, $folder_name);
			if ($file_name) {
				$returnval = $file_name;
			}
		}
		return $returnval;
	}
    
    function delete_logo()
    {
            if(!empty($_POST)){
                    $data = $this->channels_lib->getChannelsByChnlID($_POST['id']);
                    if(!empty($data))
                    {
                       
                            $save['brand_logo'] = '';
                            $this->channels_lib->update($_POST['id'], $save);
                            $this->data['json'] = array('success' => 'File Removed Successfully');

                    }else {
                    $this->data['json'] = array('error' => 'Something went wrong');
                    }

            }
            else {
                $this->data['json'] = array('error' => 'Something went wrong');
                }

    }

}