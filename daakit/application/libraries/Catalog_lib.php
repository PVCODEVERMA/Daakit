<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Catalog_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('catalog_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->catalog_model, $method)) {
            throw new Exception('Undefined method catalog_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->catalog_model, $method], $arguments);
    }

    function fetchProducts($channel_id = false)
    {

        if (!$channel_id)
            return false;
        $this->CI->load->library('channels_lib');
        if (!$channel = $this->CI->channels_lib->getByID($channel_id))
            return false;

        $config = array(
            'channel_id' => $channel_id
        );

        switch ($channel->channel) {

            case 'shopify':
                $this->CI->load->library('channels/shopify');
                $shopify = new Shopify($config);
                $product = $shopify->productFeed();
                break;

            case 'shopify_oneclick':
                $this->CI->load->library('channels/shopify');
                $shopify = new Shopify($config);
                $product = $shopify->productFeed();
                break;

            case 'woocommerce':
                $this->CI->load->library('channels/woocommerce');
                $woocommerce = new Woocommerce($config);
                $product = $woocommerce->productFeed();
                break;

            case 'magento2':
                $this->CI->load->library('channels/magento2');
                $magento2 = new Magento2($config);
                $product = $magento2->productFeed();
                break;

            case 'kwikfunnels':
                $this->CI->load->library('channels/kwikfunnels');
                $kwikfunnels = new Kwikfunnels($config);
                $product = $kwikfunnels->productFeed();
                break;

            case 'kartrocket':
                $this->CI->load->library('channels/kartrocket');
                $Kartrocket = new Kartrocket($config);
                $product = $Kartrocket->productFeed();
                break;

            case 'storehippo':
                $this->CI->load->library('channels/storehippo');
                $Storehippo = new Storehippo($config);
                $product = $Storehippo->productFeed();
                break;

            default:
                return false;
        }
        if (!empty($product)) {
            foreach ($product as $productArr) {
                $productArr['user_id'] = $channel->user_id;
                $productArr['channel_id'] = $channel_id;
                $this->insertProduct($productArr);
            }
        }
        return true;
    }

    function insertProduct($data = false)
    {
        if (empty($data))
            return false;

        $product_id = $data['product_id'];
        $channel_id = $data['channel_id'];

        $chk_product_id =  $this->getProductByChannelId($product_id, $channel_id);
        if (empty($chk_product_id)) {
            $catalog_id = $this->insert($data);
            $skuArr = array('master_sku' => 'NMB' . str_pad($catalog_id, 4, 0, STR_PAD_LEFT));
            $this->updateMasterSku($catalog_id, $skuArr);
        } else {
            $this->update($chk_product_id->id, $data);
        }
        return true;
    }
}
