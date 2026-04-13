<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Shopify extends MY_lib
{

    private $api_password;
    private $api_host;

    public function __construct($config = false)
    {
        parent::__construct();
        if (!$this->_init_properties($config['channel_id']))
            return false;
    }

    private function _init_properties($channel_id = false)
    {
        if (!$channel_id)
            return false;

        $this->CI->db->where('id', $channel_id);
        $this->CI->db->limit(1);
        $q = $this->CI->db->get('user_channels');

        if ($q->num_rows() >= 1)
            $channel = $q->row();
        else
            return false;

        $this->install_type = $channel->integration_type;

        if ($channel->integration_type == 'auto') {
            $this->api_key = $this->CI->config->item('shopify_key');
            $this->api_secret = $this->CI->config->item('shopify_secret');
        } else {
            $this->api_key = $channel->api_field_2;
            $this->api_secret = $channel->api_field_4;
        }

        $this->api_host = $channel->api_field_1;
        $this->api_password = $channel->api_field_3;

        return true;
    }
    public function productFeed()
    {
        // $this->api_password = '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';
        //$this->api_host = 'https://new-store-product.myshopify.com';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_host . '/admin/api/2021-01/products.json',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                "X-Shopify-Access-Token: " . $this->api_password
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response = json_decode($response);
        $response = $this->formatProduct($response);

        return $response;
        die;
    }
    function formatProduct($product = false)
    {
        $productData = $product->products;
        //$parentProdDate = $product;
        $data = array();
        $reqData = array();
        foreach ($productData as $productArr) {
            $data['parent_product_id'] = $productArr->id;
            //$data['product_name'] = $productArr->title;
            $data['product_status'] = $productArr->status;

            $data['product_image'] = isset($productArr->image->src) ? $productArr->image->src : '';
            $productVariants = $productArr->variants;
            foreach ($productVariants as $variants) {
                $data['product_id'] = $variants->id;
                $data['product_name'] = $productArr->title . '-' . $variants->title;
                $data['product_weight'] = $variants->grams;
                $data['product_sku'] = $variants->sku;
                $data['product_price'] = $variants->price;
                $reqData[$variants->id] = $data;
            }
        }
        return $reqData;
    }
}
