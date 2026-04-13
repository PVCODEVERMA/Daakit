<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tags_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();
    }

    function addRemoveOrderTags($ids = array(), $tags = false, $action = 'add', $user_id = false)
    {
        if (empty($ids) || empty($tags))
            return false;
        $this->addNewOrderTags($tags, $user_id);
        //fetchOrderTags for these orders
        $this->CI->load->library('orders_lib');

        foreach ($ids as $id) {
            $order = $this->CI->orders_lib->getByID($id);
            if (empty($order) || $order->user_id != $user_id)
                continue;

            $existing_tags = explode(',', $order->applied_tags);
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
            $this->CI->orders_lib->update($id, $save);
        }

        return true;
    }


    function addRemoveSingleOrderTags($ids = array(), $tags = false, $action = 'add', $user_id = false)
    {
        if (empty($ids))
            return false;
       
            $this->addNewOrderTags($tags, $user_id);

        $this->CI->load->library('orders_lib');

        $order = $this->CI->orders_lib->getByID($ids);

        $order->applied_tags = $tags;
        $save = array(
            'applied_tags' => implode(',',  $order->applied_tags)
        );
        $this->CI->orders_lib->update($ids, $save);

        return true;
    }


    function addNewOrderTags($tags = array(), $user_id = false)
    {
        if (empty($user_id) || empty($tags))
            return false;

        $this->CI->load->library('orders_lib');

        $tags = array_map('trim', $tags);
        $tags = array_map('strtolower', $tags);
        $tags = array_filter($tags);

        $existing_result = $this->CI->orders_lib->getAllTags($user_id, $tags);


        $existing_tags = array();
        foreach ($existing_result as $v) {
            $existing_tags[] = $v->tag;
        }
        $existing_tags = array_values($existing_tags);
        $new_values = array_diff($tags, $existing_tags);
        $new_values = array_filter($new_values);

        foreach ($new_values as $tag) {
            $save = array('tag' => trim(strtolower($tag)), 'user_id' => $user_id);
            $this->CI->orders_lib->insertOrderTag($save);
        }
        return true;
    }


    function addRemoveShipmentTags($ids = array(), $tags = false, $action = 'add', $user_id = false)
    {
        if (empty($ids) || empty($tags))
            return false;


        $this->CI->load->library('shipping_lib');

        foreach ($ids as $id) {
            $shipments = $this->CI->shipping_lib->getByID($id);


            $existing_tags = explode(',', $shipments->applied_tags);
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
            $this->CI->shipping_lib->update($id, $save);
        }

        return true;
    }

    function addRemoveSingleShipmentTags($ids = array(), $tags = false, $action = 'add', $user_id = false)
    {
        if (empty($ids))
            return false;

        $this->CI->load->library('shipping_lib');

        $shipments = $this->CI->shipping_lib->getByID($ids);

        $shipments->applied_tags = $tags;
        $save = array(
            'applied_tags' => implode(',',  $shipments->applied_tags)
        );
        $this->CI->shipping_lib->update($ids, $save);


        return true;
    }


    function addRemoveNDRTags($ids = array(), $tags = false, $action = 'add', $user_id = false)
    {
        if (empty($ids) || empty($tags))
            return false;


        $this->CI->load->library('ndr_lib');

        foreach ($ids as $id) {
            $ndr = $this->CI->ndr_lib->getByID($id);
            if (empty($ndr) || $ndr->user_id != $user_id)
                continue;

            $existing_tags = explode(',', $ndr->applied_tags);
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
            $this->CI->ndr_lib->update($id, $save);
        }

        return true;
    }

    function addRemoveAbandonenimgs($ids = array(), $tags = false, $action = 'add', $user_id = false)
    {
        if (empty($ids) || empty($tags))
            return false;


        $this->CI->load->library('apps/checkouts_lib');

        foreach ($ids as $id) {
            $abandoned = $this->CI->checkouts_lib->getByID($id);
            if (empty($abandoned) || $abandoned->user_id != $user_id)
                continue;

            $existing_tags = explode(',', $abandoned->applied_tags);
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
            $this->CI->checkouts_lib->update($id, $save);
        }

        return true;
    }
}
