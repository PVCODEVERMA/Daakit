<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';

class Exotelgreet extends RestController
{
   
    public function __construct()
    {
        parent::__construct('rest_api');
      
    }

  
   public function send_greet_get()
{
    // Get the CustomField parameter
    $custom = $this->input->get('CustomField');

    if (empty($custom)) {
        $response = "CustomField parameter is required";
        $this->output
            ->set_content_type('text/plain')
            ->set_output($response);
        return;
    }

    // Split the custom field into parts
    $parts = explode('|', $custom);

    if (count($parts) !== 4) {
        $response = "CustomField must contain 4 values separated by | (e.g. Hariom|12345|Laptop|Opstree)";
        $this->output
            ->set_content_type('text/plain')
            ->set_output($response);
        return;
    }

    list($customer_name, $order_number, $product_name, $seller_company_name) = $parts;

    // Generate the greeting message
    $message = "Hi, {$customer_name}! This is an order confirmation call for your order Number {$order_number} containing product {$product_name} from {$seller_company_name}. Press 1 to confirm the order and press 2 to cancel the order.";

    // Return plain text response
    $this->output
        ->set_content_type('text/plain')
        ->set_output($message);
}





}