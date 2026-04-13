<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Products_lib extends MY_lib {

    public function __construct() {
        parent::__construct();

        $this->CI->load->model('products_model');
        $this->CI->load->helper('url');

    }

    public function __call($method, $arguments){
        if (!method_exists($this->CI->products_model, $method)){
            throw new Exception('Undefined method products_model::' . $method . '() called');
        }
        return call_user_func_array([$this->CI->products_model, $method], $arguments);
    } 



    


    public function CheckUpdateProductDetails($user_id,$product){
        $save = $this->formatProductDetails($user_id,$product);
        $this->checkProductDetailsAndInsert($save,$user_id);
        $this->checkProductBillingDetailsAndInsert($save,$user_id);
    }

    public function CheckWeightApplyandReplace($user_id,$product){
        $save = $this->formatProductDetails($user_id,$product);
        $code = $this->get_product_details_code($user_id,$save);
        if(!empty($code)){
           $replace_data =  $this->getProductDetailsByCode($user_id,$code,true);
           if(!empty($replace_data)){
              $this->raplaceDimensionWeight($replace_data,$product['order_id']);
           }
        }
    }


    public function raplaceDimensionWeight($product,$order_id){
                    $save['package_length'] = $product->length;
                    $save['package_breadth'] = $product->breadth;
                    $save['package_height'] = $product->height;
                    $save['package_weight'] = $product->weight;
                    $this->CI->load->library('orders_lib'); 
                    $this->CI->orders_lib->update($order_id,$save);
                    
    }
    

    public function formatProductDetails($user_id,$product){
        if(empty($product)){
            return false;
        }
        $product_name = isset($product['product_name'])?!empty($product['product_name'])?$product['product_name']:"":"";
        $product_sku = isset($product['product_sku'])?!empty($product['product_sku'])?$product['product_sku']:"":"";
        $product_qty = isset($product['product_qty'])?!empty($product['product_qty'])?$product['product_qty']:"":"";
        $product = array(
                     "user_id"=>$user_id,
                     "product_name"=>$product_name,
                     "product_sku"=>$product_sku,
                     "product_qty"=>$product_qty
                );
        return $product;

    }

    public function get_product_details_code($user_id,$product = array()){
        $product =  $this->formatProductDetails($user_id,$product);
        $code = $product['user_id']." ".$product['product_sku']." ".$product['product_name']." ".$product['product_qty'] ;
        if(!empty($code)){
            $code  = url_title($code, 'underscore', TRUE);
            
        }
        return $code;

    }

    public function get_product_details_billing_code($user_id,$product = array()){
        $product =  $this->formatProductDetails($user_id,$product);
        $code = $product['user_id']." ".$product['product_sku']." ".$product['product_name'] ;
        if(!empty($code)){
            $code  = url_title($code, 'underscore', TRUE);
            
        }
        return $code;

    }


    function checkProductDetailsAndInsert($save,$user_id){
            $code = $this->get_product_details_code($user_id,$save);
            $save['product_details_code'] = $code;
            $existing_product = $this->getProductDetailsByCode($user_id,$code); 
            $products1 = false;
            if(empty($existing_product)){
                  $products1 = $this->insertProduct($save);
            }else{
                if(empty($existing_product->weight_locked) || $existing_product->weight_locked == '3'){
                   $products1 = $this->productDetailsUpdate($save,$existing_product->prod_id);
                }
            }
            return $products1;
    }

    function checkProductBillingDetailsAndInsert($save,$user_id){
            unset($save['product_qty']);
            $code = $this->get_product_details_billing_code($user_id,$save);
            $save['product_details_code'] = $code;
            $existing_product = $this->getProductBillingDetailsByCode($user_id,$code); 
            $products1 = false;
            if(empty($existing_product)){
                  $products1 = $this->insertProductBilling($save);
            }else{
                 $products1 = $this->productBillingDetailsUpdate($save,$existing_product->prod_id);
            }
            return $products1;
    }

    function bulkInsertUpdate($save,$user_id){
            $code = $this->get_product_details_code($user_id,$save);
            $save['product_details_code'] = $code;
            $result = false;

            if((isset($save['pid'])) &&(!empty($save['pid'])) ){
                $existing_product = $this->getProductDetailsById($user_id,$save['pid']);
                unset($save['pid']);
               
                if($existing_product){
                        unset($save['product_name']);
                        unset($save['product_qty']);
                        unset($save['product_sku']); 
                        unset($save['product_details_code']);
                    if(empty($existing_product->weight_locked) || $existing_product->weight_locked == '3'){
                         
                        $result = $this->productDetailsUpdate($save,$existing_product->prod_id);
                    }
                }
            }else{
                unset($save['pid']); 
                $existing_product = $this->getProductDetailsByCode($user_id,$code);
                if(empty($existing_product)){
                   $result = $this->insertProduct($save);
                }else{
                   $result = false;
                   $result['product_name'] = $save['product_name'];
                }
            } 
            return $result;
    }

      function bulkInsertUpdateBilling($save,$user_id){
            $code = $this->get_product_details_billing_code($user_id,$save);
            $save['product_details_code'] = $code;
            $result = false;

            if((isset($save['pid'])) &&(!empty($save['pid'])) ){
                $existing_product = $this->getProductBillingDetailsById($user_id,$save['pid']);
                unset($save['pid']);
                if($existing_product){
                  unset($save['product_name']);
                  unset($save['product_sku']);  
                  $result = $this->productBillingDetailsUpdate($save,$existing_product->prod_id);  
                }
                
            }else{
                unset($save['pid']); 
                $existing_product = $this->getProductBillingDetailsByCode($user_id,$code);
                if(empty($existing_product)){
                   $result = $this->insertProductBilling($save);
                }else{
                   $result = false;
                   $result['product_name'] = $save['product_name'];
                }
            } 
            return $result;
    }

    function getOrderProductdetails($user_id,$product_data){
            $product_order = array();
            $prod = array();
            if (!empty($product_data)) {
                foreach ($product_data as $key => $value) {
                    $prod['id'] = $value->id;
                    $prod['order_id'] = $value->order_id;
                    $prod['product_id'] = $value->product_id;
                    $prod['product_name'] = $value->product_name;
                    $prod['product_qty'] = $value->product_qty;
                    $prod['product_sku'] = $value->product_sku;
                    $prod['product_weight'] = $value->product_weight;
                    $prod['product_price'] = $value->product_price;
                    $this->CI->load->library('products_lib');
                    $code = $this->CI->products_lib->get_product_details_code($user_id,$prod);
                    if(!empty($code)){
                        $product = $this->CI->products_lib->getProductDetailsByCode($user_id,$code,true);

                            if (!empty($product)) {
                            $prod['applied_length'] = $product->length;
                            $prod['applied_breadth'] = $product->breadth;
                            $prod['applied_height'] = $product->height;
                            $prod['applied_weight'] = $product->weight;
                            $prod['applied_igst'] = $product->igst;
                            $prod['applied_hsn_code'] = $product->hsn_code;
                            $product_order = (object)$prod;
                            continue;
                        }
                    }
                }

            }
        return $product_order;
    }

    function getChargebleData($products_details_data,$order){
            $weight = isset($products_details_data->applied_weight)?$products_details_data->applied_weight:"";
            $length = isset($products_details_data->applied_length)?$products_details_data->applied_length:"";
            $breadth = isset($products_details_data->applied_breadth)?$products_details_data->applied_breadth:"";
            $height = isset($products_details_data->applied_height)?$products_details_data->applied_height:"";
            if( (!empty($weight)) && ((!empty($length))) && (!empty($breadth)) && (!empty($height)) ){
            $order->seller_applied_weight =  $weight;
            $order->seller_applied_length =  $length;
            $order->seller_applied_breadth = $breadth;
            $order->seller_applied_height =  $height;
            }
            return $order; 
            
    }

    

}
