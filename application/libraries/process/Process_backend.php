<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Process_backend extends MY_lib
{
    public function __construct()
    {
        parent::__construct();
        $this->_init();
    }

    function _init()
    {
        add_action('order_dump.new', $this, '_insertDumpOrder');
        add_action('shipping.new', $this, '_generateAWBCourierWise');
        add_action('shipping.track', $this, '_trackShipment');
        add_action('shipping.track_rto', $this, '_trackRTOShipment');
        add_action('shipping.booked', $this, '_channelFulfillment');
        add_action('shipping.cancelled', $this, '_cancelCourierAWB');
        add_action('shipping.status', $this, '_manageShippingStatus');
        add_action('shipping.status', $this, '_manageException');
        add_action('shipping.status', $this, '_updateShopifyStatus'); // for update shopify status
        add_action('shipping.status', $this, '_markShopifyFulfil'); // for marked shopify fulfilled
        add_action('order.cancelled', $this, '_cancelOrderAtChannel');
        add_action('channel.create', $this, '_newChannelAdded');
        add_action('channel.create', $this, '_addWebhook');
        add_action('channels.refreshOrders', $this, '_getChannelNewOrders');
        add_action('invoice.generate', $this, '_generateNewInvoice');
        add_action('log.create', $this, '_processCloudwatchLog');
    }

    function _insertDumpOrder($dump_id = false)
    {
        if (!$dump_id)
            return false;

        $this->CI->load->library('orders_lib');
        $this->CI->orders_lib->processDumpOrder($dump_id);
    }

    function _cancelCourierAWB($shipment_id = false)
    {
        if (!$shipment_id)
            return false;

        $this->CI->load->library('shipping_lib');
        $this->CI->shipping_lib->cancelShipmentAtcourier($shipment_id);
        return true;
    }

    function _generateNewInvoice($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->CI->load->library('generate_invoice');
        $invoice = new Generate_invoice(array('user_id' => $user_id, 'type' => 'shipment'));
        $invoice->generateInvoiceForUser();
    }

    function _cancelOrderAtChannel($order_id = false)
    {
        if (!$order_id)
            return false;

        $this->CI->load->library('orders_lib');
        $this->CI->orders_lib->cancelChannelOrder($order_id);
    }

    function _manageShippingStatus($shipment_id = false, $event = false)
    {
        if (!$shipment_id || empty($event))
            return false;

        $status = strtolower($event['ship_status']);

        $this->CI->load->library('status_lib');

        $shipment_array = $event;

        $shipment_array['shipment_id'] = $shipment_id;

        $status_lib = new Status_lib($shipment_array);

        $status_lib->updateStatus();

        $this->_pushShippingStatus($shipment_id, $status);

        return true;
    }

    function _pushShippingStatus($shipment_id = false, $status = false)
    {
        
        if (!$shipment_id || !$status)
            return false;

        $status = str_replace(' ', '_', $status);
        $channel = $this->getChannelByShipmentId($shipment_id);
            if (ENVIRONMENT != 'development') {
                $this->CI->load->library('events');
                $this->CI->events->send_to_exchange('shipment', array('shipment_id' => $shipment_id,'channel'=>$channel), true, "shipment.status.{$status}");
            }
        
    }


    function getChannelByShipmentId($shipment_id,$by = "shipment"){
        $channel = "";
        if($by=="shipment"){
            $this->CI->load->library('shipping_lib');
            $shipment = $this->CI->shipping_lib->getByID($shipment_id);
            $order_id = $shipment->order_id;
        }else{
            $order_id = $shipment_id; 
        }
        $this->CI->load->library('channels_lib');
        $this->CI->load->library('orders_lib');
        $order = $this->CI->orders_lib->getByID($order_id);
        if(isset($order->channel_id) && !empty($order->channel_id)) {
        $channel = $this->CI->channels_lib->getByID($order->channel_id);
        $channel = isset($channel->channel)?strtolower($channel->channel):"";
        if($channel=='shopify_oneclick')
        $channel = "shopify"; 
        }
        return $channel;

    }

    function _manageException($shipment_id = false, $event = false)
    {
        $this->CI->load->library('ndr_lib');
        $this->CI->ndr_lib->manageNDRAgainstShipStatus($shipment_id, $event);
        return true;
    }

    function _savePickupTime($shipment_id = false)
    {
        if (!$shipment_id)
            return false;

        $this->CI->load->library('shipping_lib');
        $this->CI->shipping_lib->savePickupTime($shipment_id);
        return true;
    }

    function _saveDeliveredTime($shipment_id = false)
    {
        if (!$shipment_id)
            return false;

        $this->CI->load->library('shipping_lib');
        $this->CI->shipping_lib->saveDeliveredTime($shipment_id);
        return true;
    }

    function _markPickupRequest($shipment_id = false)
    {
        if (!$shipment_id)
            return false;

        $this->CI->load->library('pickups_lib');
        $this->CI->pickups_lib->markPickedUP($shipment_id);
        return true;
    }

    function _generateAWB($shipment_id = false)
    {
        if (empty($shipment_id))
            return false;

        if (ENVIRONMENT != 'development') {
            $this->CI->load->library('events');
            $this->CI->events->send_to_exchange('shipment', array('shipment_id' => $shipment_id), true, 'shipment.generate_awb.others');
        }
    }

    function _generateAWBCourierWise($shipment_id = false, $courier_id = false)
    {
        if (empty($shipment_id) || empty($courier_id))
            return false;

        $this->CI->load->library('shipping_lib');
        $this->CI->shipping_lib->processShipment($shipment_id);
    }

    function _trackShipment($shipment_id = false)
    {
        if (empty($shipment_id))
            return false;

        $this->CI->load->library('shipping_lib');
        $this->CI->shipping_lib->getTrackingHistoryLive($shipment_id);
    }

    function _trackRTOShipment($shipment_id = false)
    {
        if (empty($shipment_id))
            return false;
        $this->CI->load->library('shipping_lib');
        $this->CI->shipping_lib->getTrackingHistoryLive($shipment_id,true);
    }

    function _getChannelNewOrders($channel_id = false, $channel = false)
    {

        if (empty($channel_id))
            return false;

        $this->CI->load->library('orders_lib');
        $this->CI->orders_lib->webhookOrders($channel_id);
    }

    function _channelFulfillment($shipment_id = false, $channel = '')
    {

        if (empty($shipment_id))
            return false;

        $this->CI->load->library('shipping_lib');
        $this->CI->shipping_lib->markChannelOrderFulfill($shipment_id);
    }

    function _newChannelAdded($channel_id = false)
    {
        if (!$channel_id)
            return false;

        $this->CI->load->library('orders_lib');
        $this->CI->orders_lib->fetchOrders($channel_id);

        return true;
    }

    function _addWebhook($channel_id = false)
    {
        $this->CI->load->library('channels_lib');

        $channel = $this->CI->channels_lib->getBYID($channel_id);
        if (empty($channel))
            return false;

        switch ($channel->channel) {
            case 'shopify':
            case 'shopify_oneclick':
                $config = array(
                    'channel_id' => $channel_id
                );
                $load_name = 'shopify_' . $channel_id;
                $this->CI->load->library('channels/shopify', $config, $load_name);
                $this->CI->{$load_name}->createOrderWebhook(base_url('response/channel_webhook/' . $channel_id));
                $this->CI->{$load_name}->createOrderWebhook(base_url('response/channel_webhook/' . $channel_id), 'cancelled');
                $this->CI->{$load_name}->createOrderWebhook(base_url('response/channel_webhook/' . $channel_id), 'updated');
                break;
            default:
                return false;
        }
        return true;
    }

    function _markShopifyFulfil($shipment_id = '')
    {
        $this->CI->load->library('shipping_lib');
        $this->CI->shipping_lib->markChannelOrderFulfill($shipment_id); // mark order for fullfillment in case of shopify 
    }

    function _updateShopifyStatus($shipment_id = false)
    {
        $this->CI->load->library('shipping_lib');
        $this->CI->shipping_lib->pushShopifyStatus($shipment_id); // mark order for fullfillment in case of shopify 
    }

    function _processCloudwatchLog($entity_type = 'default', $data = null, $level = 'INFO')
    {
        $action=!empty($data['action'])? $data['action'] : '';
        $user_id=!empty($data['user_id'])? '_'.$data['user_id'] : '';
        $ref_id=!empty($data['ref_id'])? '_'.$data['ref_id'] : '';
        $action_type = $action . $user_id . $ref_id;
        $data['user_id']=!empty($data['user_id']) ? $data['user_id'] : '';
        $log = new Logs();
        $log->create($entity_type, $action_type, $data, $data['user_id']);
    }

}
