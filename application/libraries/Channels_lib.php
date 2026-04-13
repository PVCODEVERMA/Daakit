<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Channels_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();
        $this->CI->load->model('channels_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->channels_model, $method)) {
            throw new Exception('Undefined method channels_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->channels_model, $method], $arguments);
    }

    function updateBywhatsup($order_id , $channel , $data){
        
        $this->CI->load->library('orders_lib');
        $order = $this->CI->orders_lib->getByID($order_id);
        
        if(empty($order))
        return false;
        $order_id = $order->api_order_id;
        $channel_id = $order->channel_id;
        switch ($channel) {
            case 'shopify':
                $config = array(
                    'channel_id' => $channel_id
                );
                $this->CI->load->library('channels/shopify');
                $shopify = new Shopify($config);
                $shopify->whatsupConfirm($order_id,$channel_id,$data);
                break;


            case 'woocommerce':
                $config = array(
                    'channel_id' => $channel_id
                );
                $load_name = 'woocommerce_' . $channel_id;
                $this->CI->load->library('channels/woocommerce', $config, $load_name);
                $this->CI->{$load_name}->whatsupConfirm($order_id,$channel_id,$data);
                break;
            default:
            return false;
        }

    }

}
