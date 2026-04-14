<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';

class Unicommerce extends RestController

{
     var $account_id = false;

      public function __construct()
    {
        parent::__construct('rest_api');
        $this->load->library('orders_lib');
        $this->load->library('warehouse_lib');
        $this->load->library('user_lib');

      $method = $this->router->fetch_method();

if ($method !== 'authToken') {
    $this->validateAPIToken();
}
        
    }

     private function validateAPIToken()
    {
        $this->load->library('jwt_lib');

        try {
            $api_data = $this->jwt_lib->validateAPIS();
            if ($api_data->parent_id == '0')
                $this->account_id = $api_data->user_id;
            else
                $this->account_id = $api_data->parent_id;

    
        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'message' => $e->getMessage(),
            ], 401);
        }
    }



public function authToken_post()
    {
        $input_json = $this->input->raw_input_stream;
        $input_data = json_decode($input_json, true);

        $this->load->library('form_validation');
        $this->form_validation->set_data($input_data);

        $config = array(
            array(
                'field' => 'username',
                'label' => 'User Name',
                'rules' => 'trim|required|min_length[3]|max_length[200]|valid_email'
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|required|min_length[6]|max_length[50]'
            )
        );

        $this->form_validation->set_rules($config);

        if (!$this->form_validation->run()) {
            $this->response([
                'status' => "INVALID_CREDENTIALS",
                'message' => strip_tags(validation_errors())
            ], 400);
        }

        $token = $this->user_lib->userApiLogin($input_data['username'], $input_data['password']);
        if (!$token) {
            $this->response([
                'status' => "INVALID_CREDENTIALS",
                'message' => $this->user_lib->get_error(),
            ], 401);
        }

        $this->response([
            'status' => "SUCCESS",
            'token' => $token
        ], 200);
    }




// function waybill_post()
// {

//     $startTime = microtime(true);
//     $timings = [];
//     $input_json = $this->input->raw_input_stream;
//     $data = json_decode($input_json, true);
//      $timings['parse_input'] = microtime(true) - $startTime;

//     if (!$data || !isset($data['Shipment'])) {
//         return $this->formattedError('Invalid or missing shipment data.', 400);
//     }
//      $checkpoint = microtime(true);

//     if (isset($data['returnShipmentFlag']) && strtolower($data['returnShipmentFlag']) === 'true') {
//     return $this->formattedError('Return shipments are not allowed through this endpoint.', 400);
//     }
//     $timings['check_return_shipment'] = microtime(true) - $checkpoint;
//      $checkpoint = microtime(true);

//     // Step 1: Validate required fields
//     $validation = $this->validateRequiredFields($data);
//      $timings['validate_fields'] = microtime(true) - $checkpoint;
//     if ($validation['status'] === false) {
//         return $this->formattedError('Missing required fields: ' . implode(', ', $validation['missing']), 400);
//     }

//     // Step 2: Prepare data
//      $checkpoint = microtime(true);
//     $orderData = $this->prepareOrderData($data);
//     $timings['prepare_order'] = microtime(true) - $checkpoint;
//     $items = $data['Shipment']['items'];
//     $pickup = $data['pickupAddressDetails'];
//     $return = $data['returnAddressDetails'];

//     // Optional parameters
//     $courier_id = $data['courier_id'] ?? '';
//     $essential_order = $data['essential_order'] ?? false;
//     $dg_order = $data['dg_order'] ?? false;
//     $is_insurance = $data['is_insurance'] ?? false;
//     $user_id = $this->account_id;
//     $tags = $data['tags'] ?? ''; 

//     // Begin transaction
//      $checkpoint = microtime(true);
//     $this->db->trans_start();

//     // Save order
//     $order_id = $this->orders_lib->insertOrder($orderData);
//     $timings['insert_order'] = microtime(true) - $checkpoint;
//     if (!$order_id) {
//         $this->db->trans_rollback();
//         return $this->formattedError('Failed to save order.', 500);
//     }

//     // Save items
//     // foreach ($items as $item) {
//     //     $productData = $this->prepareProductData($order_id, $item, $data['Shipment']);
//     //     if (!$this->orders_lib->insertProduct($productData)) {
//     //         $this->db->trans_rollback();
//     //         return $this->formattedError('Failed to save product: ' . $item['skuCode'], 500);
//     //     }
//     // }
    
//     $productDataArray = [];

// foreach ($items as $item) {
//     $productDataArray[] = $this->prepareProductData($order_id, $item, $data['Shipment']);
// }

// // Use batch insert
// if (!empty($productDataArray)) {
//     if (!$this->orders_lib->insertProductsBatch($productDataArray)) {
//         $this->db->trans_rollback();
//         return $this->formattedError('Failed to save products in batch.', 500);
//     }
// }

//     // Save warehouses
//     $warehousepickup = $this->formatWarehouse($pickup, $order_id);
//     $warehousereturn = $this->formatWarehouse($return, $order_id);


//     $warehouse_id = $this->matchWarehouse($warehousepickup);
//     if (!$warehouse_id) {
//         $this->db->trans_rollback();
//         return $this->formattedError('Failed to process pickup warehouse.', 500);
//     }  

//     $rto_warehouse_id = $this->matchWarehouse( $warehousereturn);
//     if (!$rto_warehouse_id) {
//         $this->db->trans_rollback();
//         return $this->formattedError('Failed to process return warehouse.', 500);
//     }

//     // $warehouse_id = $this->warehouse_lib->create($warehousepickup);
//     // $rto_warehouse_id = $this->warehouse_lib->create($warehousereturn);
    

//     if (!$warehouse_id || !$rto_warehouse_id) {
//         $this->db->trans_rollback();
//         return $this->formattedError('Failed to save pickup/return warehouse.', 500);
//     }

//     // $this->warehouse_lib->createUpdateWarehouseWithCourier($warehouse_id);
//     // $this->warehouse_lib->createUpdateWarehouseWithCourier($rto_warehouse_id);

//    // Process shipment
//     //$shipment_id = $this->orders_lib->processOrderShipment($order_id, $courier_id, $user_id, $warehouse_id, $rto_warehouse_id, $essential_order, $dg_order, $is_insurance);
//     $checkpoint = microtime(true);
//     $awb_data= $this->orders_lib->shipAPIOrder($order_id, $this->account_id, $warehouse_id, $rto_warehouse_id, $selected_courier = 'autoship', $essential_order, $dg_order, $is_insurance,true, $tags);
//      $timings['ship_api_order'] = microtime(true) - $checkpoint;
//     if (!$awb_data) {
//         $this->db->trans_rollback();
//         return $this->formattedError($this->orders_lib->get_error(), 500);

//     }

  
//    $shipment_id=$awb_data['shipment_id'];
//      // Generate label
//     $this->load->library('shipping_lib');
//     $checkpoint = microtime(true);
//     $label_data = $this->shipping_lib->generateLabel([$shipment_id], 'thermal',  $user_id);
//     $timings['generate_label'] = microtime(true) - $checkpoint;
//     if (!$label_data) {
//         $this->db->trans_rollback();
//         return $this->formattedError('Failed to generate shipping label.', 500);
//     }

//        // Fetch AWB no.
//     $shipping = $this->db->get_where('tbl_order_shipping', ['id' => $shipment_id])->row();
//     if (!$shipping || empty($shipping->awb_number)) {
//         $this->db->trans_rollback();
//         return $this->formattedError('AWB number not found.', 500);
//     }

//     $courier = $this->db->get_where('tbl_courier', ['id' => $shipping->courier_id])->row();
//     if ($courier && !empty($courier->name)) {
//         $courier_name = $courier->name;
//     }

//     $this->db->trans_complete();

//     if ($this->db->trans_status() === FALSE) {
//         return $this->formattedError('Transaction failed.', 500);
//     }
//     $endTime = microtime(true);
//     $timings['total_time'] = $endTime - $startTime;

//     // Final response
//     return $this->response([
//         'status' => 'SUCCESS',
//         'waybill' => $shipping->awb_number,
//         'shippingLabel' => $label_data,
//         'courierName' => $courier_name,
//         'routingCode'=> '' ,
//          'timings' => $timings
//     ], 200);
// }






function waybill_post()
{   
    $input_json = $this->input->raw_input_stream;
    $data = json_decode($input_json, true);

    if (!$data || !isset($data['Shipment'])) {
        return $this->formattedError('Invalid or missing shipment data.', 400);
    }

    // Step 1: Validate required fields
    $validation = $this->validateRequiredFields($data);
     $timings['validate_fields'] = microtime(true) - $checkpoint;
    if ($validation['status'] === false) {
        return $this->formattedError('Missing required fields: ' . implode(', ', $validation['missing']), 400);
    }

    if (isset($data['returnShipmentFlag']) && $data['returnShipmentFlag'] === true) {
    return $this->formattedError('Return shipments are not allowed through this endpoint.', 400);
    }

    // Step 2: Prepare data
    $orderData = $this->prepareOrderData($data);
    $items = $data['Shipment']['items'];
    $pickup = $data['pickupAddressDetails'];
    $return = $data['returnAddressDetails'];

    // Optional parameters
    $courier_id = $data['courier_id'] ?? '';
    $essential_order = $data['essential_order'] ?? false;
    $dg_order = $data['dg_order'] ?? false;
    $is_insurance = $data['is_insurance'] ?? false;
    $user_id = $this->account_id;
    $tags = $data['tags'] ?? ''; 

    // Begin transaction
    $this->db->trans_start();

    // Save order
    $order_id = $this->orders_lib->insertOrder($orderData);
    if (!$order_id) {
        $this->db->trans_rollback();
        return $this->formattedError('Failed to save order.', 500);
    }
    
    $productDataArray = [];

    foreach ($items as $item) {
      $productDataArray[] = $this->prepareProductData($order_id, $item, $data['Shipment']);
    }

// Use batch insert
    if (!empty($productDataArray)) {
      if (!$this->orders_lib->insertProductsBatch($productDataArray)) {
        $this->db->trans_rollback();
        return $this->formattedError('Failed to save products in batch.', 500);
      }
    }

    // Save warehouses
    $warehousepickup = $this->formatWarehouse($pickup, $order_id);
    $warehousereturn = $this->formatWarehouse($return, $order_id);


    $warehouse_id = $this->matchWarehouse($warehousepickup);
    if (!$warehouse_id) {
        $this->db->trans_rollback();
        return $this->formattedError('Failed to process pickup warehouse.', 500);
    }  

    $rto_warehouse_id = $this->matchWarehouse($warehousereturn);
    if (!$rto_warehouse_id) {
        $this->db->trans_rollback();
        return $this->formattedError('Failed to process return warehouse.', 500);
    }

    // $warehouse_id = $this->warehouse_lib->create($warehousepickup);
    // $rto_warehouse_id = $this->warehouse_lib->create($warehousereturn);
    

    if (!$warehouse_id || !$rto_warehouse_id) {
        $this->db->trans_rollback();
        return $this->formattedError('Failed to save pickup/return warehouse.', 500);
    }

    // $this->warehouse_lib->createUpdateWarehouseWithCourier($warehouse_id);
    // $this->warehouse_lib->createUpdateWarehouseWithCourier($rto_warehouse_id);

   // Process shipment
    //$shipment_id = $this->orders_lib->processOrderShipment($order_id, $courier_id, $user_id, $warehouse_id, $rto_warehouse_id, $essential_order, $dg_order, $is_insurance);
    $awb_data= $this->orders_lib->shipAPIOrder($order_id, $this->account_id, $warehouse_id, $rto_warehouse_id, $selected_courier = 'autoship', $essential_order, $dg_order, $is_insurance,true, $tags);
    if (!$awb_data) {
        $this->db->trans_rollback();
        return $this->formattedError($this->orders_lib->get_error(), 500);

    }
  
    $shipment_id=$awb_data['shipment_id'];
     // Generate label
    $this->load->library('shipping_lib');
    $label_data = $this->shipping_lib->generateLabel([$shipment_id], 'thermal',  $user_id);
    $timings['generate_label'] = microtime(true) - $checkpoint;
    if (!$label_data) {
        $this->db->trans_rollback();
        return $this->formattedError('Failed to generate shipping label.', 500);
    }

       // Fetch AWB no.
    $shipping = $this->db->get_where('tbl_order_shipping', ['id' => $shipment_id])->row();
    if (!$shipping || empty($shipping->awb_number)) {
        $this->db->trans_rollback();
        return $this->formattedError('AWB number not found.', 500);
    }

    $courier = $this->db->get_where('tbl_courier', ['id' => $shipping->courier_id])->row();
    if ($courier && !empty($courier->name)) {
        $courier_name = $courier->name;
    }

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        return $this->formattedError('Transaction failed.', 500);
    }

    $this->load->library('pickups_lib');

    $shipment_ids = [$shipment_id];
    $pickup_ids = [];
    $manifest = '';

    try {
            // Schedule pickup
            $pickup_ids = $this->shipping_lib->schedulePickup(
                $this->account_id,
                $shipment_ids
            );

            // Generate manifest
            if (!empty($pickup_ids)) {
                $manifest = $this->pickups_lib->download_manifest(
                    $pickup_ids,
                    $this->account_id
                );
            }

    } catch (Throwable $e) {
        log_message('error', 'Pickup/Manifest Error: ' . $e->getMessage());
    }

    // Final response
    return $this->response([
        'status' => 'SUCCESS',
        'waybill' => $shipping->awb_number,
        'shippingLabel' => $label_data,
        'courierName' => $courier_name,
        'routingCode'=> ''
    ], 200);
}


private function validateRequiredFields($data)
{
    $requiredFields = [
        'returnShipmentFlag',
        'Shipment.code',
        'Shipment.orderCode',
        'Shipment.SaleOrderCode',
        'Shipment.weight',
        'Shipment.length',
        'Shipment.breadth',
        'Shipment.height',
        'Shipment.numberOfBoxes',
        'Shipment.items',
        'Shipment.items.name',
        'Shipment.items.description',
        'Shipment.items.quantity',
        'Shipment.items.skuCode',
        'Shipment.items.itemPrice',
        'pickupAddressDetails.name',
        'pickupAddressDetails.phone',
        'pickupAddressDetails.address1',
        'pickupAddressDetails.pincode',
        'pickupAddressDetails.city',
        'pickupAddressDetails.state',
        'pickupAddressDetails.country',
        'returnAddressDetails.name',
        'returnAddressDetails.phone',
        'returnAddressDetails.address1',
        'returnAddressDetails.pincode',
        'returnAddressDetails.city',
        'returnAddressDetails.state',
        'returnAddressDetails.country',
        'deliveryAddressDetails.name',
        'deliveryAddressDetails.phone',
        'deliveryAddressDetails.address1',
        'deliveryAddressDetails.pincode',
        'deliveryAddressDetails.city',
        'deliveryAddressDetails.state',
        'deliveryAddressDetails.country',
        'currencyCode',
        'paymentMode',
        'totalAmount',
        'collectableAmount'
    ];

   $missingFields = [];

foreach ($requiredFields as $field) {
    
    if (strpos($field, 'Shipment.items.') === 0) {
        if (!isset($data['Shipment']['items']) || !is_array($data['Shipment']['items']) || count($data['Shipment']['items']) === 0) {
            $missingFields[] = 'Shipment.items';
        } else {
            $itemField = str_replace('Shipment.items.', '', $field);
            foreach ($data['Shipment']['items'] as $index => $item) {
                if (!isset($item[$itemField]) || $item[$itemField] === '') {
                    $missingFields[] = "Shipment.items[$index].$itemField";
                }
            }
        }
    } else {
        
        $parts = explode('.', $field);
        $temp = $data;
        foreach ($parts as $part) {
            if (!isset($temp[$part]) || $temp[$part] === '') {
                $missingFields[] = $field;
                break;
            }
            $temp = $temp[$part];
        }
    }
}


    return empty($missingFields)
        ? ['status' => true]
        : ['status' => false, 'missing' => $missingFields];
}

private function prepareOrderData($data)
{
    $now = time();
    $delivery = $data['deliveryAddressDetails'];
    $shipping = $billing = $delivery;

    $length_cm = isset($data['Shipment']['length']) ? $data['Shipment']['length'] / 10 : 0;
    $breadth_cm = isset($data['Shipment']['breadth']) ? $data['Shipment']['breadth'] / 10 : 0;
    $height_cm = isset($data['Shipment']['height']) ? $data['Shipment']['height'] / 10 : 0;

    return [
        'order_type'           => 'ecom',
        'user_id'              => $this->account_id,
        'order_no'             => $data['Shipment']['orderCode'] . '-' . $data['Shipment']['SaleOrderCode'] . '-' . ($data['Shipment']['channelCode'] ?? ''),
        'order_date'           => strtotime($data['Shipment']['orderDate'] ?? date('Y-m-d H:i:s')),
        'order_amount'         => $data['totalAmount'],
        'order_payment_type'   => $data['paymentMode'],
        'customer_name'        => $delivery['name'],
        'customer_phone'       => $delivery['phone'],
        'customer_email'       => $delivery['email'] ?? '',
        'shipping_fname'       => $shipping['name'],
        'shipping_lname'       => '',
        'shipping_email'       => $shipping['email'] ?? '',
        'shipping_phone'       => $shipping['phone'],
        'shipping_address'     => $shipping['address1'],
        'shipping_address_2'   => $shipping['address2'] ?? '',
        'shipping_city'        => $shipping['city'],
        'shipping_state'       => $shipping['state'],
        'shipping_country'     => $shipping['country'],
        'shipping_zip'         => $shipping['pincode'],
        'shipping_gst_number'  => $shipping['gstin'] ?? '',
        'billing_fname'        => $billing['name'],
        'billing_lname'        => '',
        'billing_phone'        => $billing['phone'],
        'billing_address'      => $billing['address1'],
        'billing_address_2'    => $billing['address2'] ?? '',
        'billing_city'         => $billing['city'],
        'billing_state'        => $billing['state'],
        'billing_country'      => $billing['country'],
        'billing_zip'          => $billing['pincode'],
        'billing_gst_number'   => $billing['gstin'] ?? '',
        'package_weight'       => $data['Shipment']['weight'],
        'package_length'       => $length_cm,
        'package_breadth'      => $breadth_cm,
        'package_height'       => $height_cm,
        'no_of_boxes'          => $data['Shipment']['numberOfBoxes'],
        'latitude'             => $delivery['latitude'] ?? '',
        'longitude'            => $delivery['longitude'] ?? '',
        'order_source'         => $data['Shipment']['source'] ?? '',
        'collectable_amount'   => $data['collectableAmount'] ?? '0',
    ];
}

private function prepareProductData($order_id, $item, $shipment)
{
    return [
        'order_id'      => $order_id,
        'user_id'       => $this->account_id,
        'product_name'  => $item['name'],
        'product_qty'   => $item['quantity'],
        'product_sku'   => !empty($item['hsnCode']) ? $item['skuCode'] . '-' . $item['hsnCode'] : $item['skuCode'],
        'product_price' => $item['itemPrice'],
        'product_weight'=> $shipment['weight'] ?? null,
    ];
}

private function formatWarehouse($warehouseData, $order_id)
{
    return [
        'user_id'      => $this->account_id,
        'name'         => $warehouseData['name'],
        'contact_name'  => $warehouseData['name'],
        'phone'        => $warehouseData['phone'],
        'email'        => $warehouseData['email'] ?? '',
        'address_1'    => $warehouseData['address1'],
        'address_2'    => $warehouseData['address2'] ?? '',
        'zip'          => $warehouseData['pincode'],
        'city'         => $warehouseData['city'],
        'state'        => $warehouseData['state'],
        'country'      => $warehouseData['country'],
        'gst_number'   => $warehouseData['gstin'] ?? '',
        'latitude'     => $warehouseData['latitude'] ?? '',
        'longitude'    => $warehouseData['longitude'] ?? '',
        // 'warehouse_all_details'   => md5(url_title(trim(strtolower($warehouseData['name'])) . "" . trim(strtolower($warehouseData['address1'])) . "" . trim(strtolower($warehouseData['city'])) . "" . trim(strtolower($warehouseData['state'])) . "" . trim($warehouseData['pincode']) . "" . trim($warehouseData['phone']))),
        // 'active'                  => '1'
    ];

}

private function matchWarehouse($warehouse = array())
    {
        if (empty($warehouse['name']))
            return false;

        //first check if existing warehouse by name
        
        $warehouse_c = md5(url_title_address(trim(strtolower($warehouse['contact_name']))."".trim(strtolower($warehouse['address_1']))."".trim(strtolower($warehouse['city']))."".trim(strtolower($warehouse['state']))."".trim($warehouse['zip'])."".trim($warehouse['phone'])));

        $existing_warehouse = $this->warehouse_lib->getUserWarehouseByDetails($this->account_id, $warehouse_c);
  
        if ($existing_warehouse) {
            //match all keys with the posted data

            if ($existing_warehouse->warehouse_all_details == $warehouse_c)
                return $existing_warehouse->id;
        }

        //create warehosue using the details
        $save = array(
            'user_id' => $this->account_id,
            'name' => 'DKT_' . time(),
            'contact_name' => $warehouse['name'],
            'phone' => $warehouse['phone'],
            'address_1' => $warehouse['address_1'],
            'address_2' => isset($warehouse['address_2']),
            'city' => $warehouse['city'],
            'state' => $warehouse['state'],
            'country' => $warehouse['country'],
            'zip' => $warehouse['zip'],
            'gst_number' => $warehouse['gst_number'],
            'latitude'     => $warehouse['latitude'],
            'longitude'     => $warehouse['longitude'],
            'warehouse_all_details' => $warehouse_c,
            'active' => '1'
        );

        $new_id = $this->warehouse_lib->create($save);
        $this->warehouse_lib->createUpdateWarehouseWithCourier($new_id);

        return $new_id;
    }

private function formattedError($reason, $httpCode = 400)
{
    return $this->response([
        'status' => 'FAILED',
        'reason' => $reason,
        'message' => $reason
    ], $httpCode);
}

public function cancel_post()
{

    $input_json = $this->input->raw_input_stream;
    $data = json_decode($input_json, true);

    if (empty($data['waybill'])) {
        echo json_encode([
            "status" => "FAILED",
            "waybill" => "",
            "errorMessage" => "Waybill (AWB) is required"
        ]);
        return;
    }

    $awb = $data['waybill'];

    $this->load->library('shipping_lib');

   
    $shipment = $this->shipping_lib->getByAWB($awb, $this->account_id);

    if (empty($shipment)) {
        echo json_encode([
            "status" => "FAILED",
            "waybill" => $awb,
            "errorMessage" => "Shipment not found for given AWB"
        ]);
        return;
    }

    
    $cancelResult = $this->shipping_lib->cancelShipment($shipment->id, $this->account_id);

    if ($cancelResult === true) {
        echo json_encode([
            "status" => "SUCCESS",
            "waybill" => $awb,
            "errorMessage" => "Shipment successfully cancelled"
        ]);
    } else {
        echo json_encode([
            "status" => "FAILED",
            "waybill" => $awb,
            "errorMessage" => !empty($this->shipping_lib->get_error()) ? $this->shipping_lib->get_error() : "Shipment could not be cancelled due to an internal error"
        ]);
    }
}


function waybillDetails_get()
{
   $waybillParam = $this->input->get_post('waybills');  // Accepts from GET or POST
$waybillParam = trim($waybillParam, '"');
$waybills = array_map('trim', explode(',', $waybillParam));

if (empty($waybills)) {
    return $this->response([
        'status' => 'FAILED',
        'message' => 'Missing required parameter: waybill',
        'waybillDetails' => []
    ], 400);
}


    if (count($waybills) > 50) {
        return $this->response([
            'status' => 'FAILED',
            'message' => 'Maximum 50 waybills are allowed per request',
            'waybillDetails' => []
        ], 400);
    }

    $responseData = [];

    foreach ($waybills as $awb) {
        $this->db->order_by('event_time', 'ASC');
        $tracking = $this->db->get_where('tbl_awb_tracking', ['awb_number' => $awb])->result();
        $shipment = $this->db->get_where('tbl_order_shipping', ['awb_number' => $awb])->row();
        $courier = $this->db->get_where('tbl_courier', ['id' => $shipment->courier_id])->row();
        if ($courier && !empty($courier->name)) {
           $courier_name = $courier->name;
        }

        if ($tracking) {
            $latest = end($tracking);
            $tracking_history = [];

            foreach ($tracking as $entry) {
                $tracking_history[] = [
                    'date_time' => date('d-M-Y H:i:s', $entry->event_time),
                    'status' => $entry->ship_status,
                    'sub_status' => '',
                    'remark' => $entry->message,
                    'location' => $entry->location,
                    'pincode' => '', 
                    'city' => '', 
                    'state' => '', 
                    'country' => 'India'
                ];
            }

            $waybill_detail = [
                'waybill' => $awb,
                'currentStatus' => $latest->ship_status,
                'current_sub_status' => '',
                'current_status_remark' => $latest->message,
                'statusDate' => date('d-M-Y H:i:s', $latest->event_time),
                'shipping_provider' => $courier_name,
                'current_location' => $latest->location,
                'current_pincode' => '',
                'current_city' => '',
                'current_state' => '',
                'current_country' => 'India',
                'latitude' => '',
                'longitude' => '',
                'expected_date_of_delivery' => '',
                'promised_date_of_delivery' => '',
                'payment_type' => '',
                'weight' => '',
                'dimensions' => [
                    'l' => '',
                    'b' => '',
                    'h' => ''
                ],
                'delivery_agent_name' => '',
                'delivery_agent_number' => '',
                'attempt_count' => '',
                'ndr_code' => '',
                'ndr_reason' => '',
                'next_delivery_date' => '',
                'cir_pickup_datetime' => '',
                'tracking_history' => $tracking_history,
                'parent_awb' => '',
                'rto_awb' => $latest->rto_awb,
                'rto_reason' => ''
            ];
        } else {
            $shipping = $this->db->get_where('tbl_order_shipping', ['awb_number' => $awb])->row();

            if (!$shipping) {
                continue;
            }

            $waybill_detail = [
                'waybill' => $awb,
                'currentStatus' => $shipping->ship_status,
                'current_sub_status' => '',
                'current_status_remark' => $shipping->message,
                'statusDate' => date('d-M-Y H:i:s', $shipping->status_updated_at),
                'shipping_provider' => $courier_name,
                'current_location' => '',
                'current_pincode' => '',
                'current_city' => '',
                'current_state' => '',
                'current_country' => 'India',
                'latitude' => '',
                'longitude' => '',
                'expected_date_of_delivery' => date('d-M-Y H:i:s', $shipping->edd_time),
                'promised_date_of_delivery' => '',
                'payment_type' => $shipping->payment_type,
                'weight' => $shipping->charged_weight,
                'dimensions' => [
                    'l' => $shipping->courier_length,
                    'b' => $shipping->courier_breadth,
                    'h' => $shipping->courier_height
                ],
                'delivery_agent_name' => '',
                'delivery_agent_number' => '',
                'attempt_count' => '',
                'ndr_code' => '',
                'ndr_reason' => '',
                'next_delivery_date' => '',
                'cir_pickup_datetime' => '',
                'tracking_history' => [],
                'parent_awb' => '',
                'rto_awb' => $shipping->rto_awb,
                'rto_reason' => ''
            ];
        }

        $responseData[] = $waybill_detail;
    }

     if (empty($responseData)) {
        return $this->response([
            'status' => 'FAILED',
            'message' => 'No valid waybill data found.',
            'waybillDetails' => []
        ], 404);
    }

    return $this->response([
        'Status' => 'SUCCESS',
        'waybillDetails' => $responseData,
        'message' => 'Waybill status fetched successfully'
    ], 200);
}



// function runWaybillGeneration($payload)
// {
//     $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE3NDk2MjMyOTEsImp0aSI6IndEOFA5WEk0K011K00xb0R2TnZzZGd3VUNwVjR1M09nTHhENU00MW5wTGs9IiwibmJmIjoxNzQ5NjIzMjkxLCJleHAiOjE3NDk2MzQwOTEsImRhdGEiOnsidXNlcl9pZCI6IjYwIiwicGFyZW50X2lkIjoiMCJ9fQ.76yMi8wSSt0_yePheTsjU7qNsHuf-K5Kpnz3qI9pQVAkjYDUqXrhWvDyJavOnrVjsSxsgA1JfBOZr0HzWlBvlg';

//     $ch = curl_init();

//     curl_setopt($ch, CURLOPT_URL, 'http://localhost/daakit/index.php/api/unicommerce/waybill');
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
//     curl_setopt($ch, CURLOPT_HTTPHEADER, [
//         'Content-Type: application/json',
//         'Authorization: Bearer ' . $token
//     ]);

//     $response = curl_exec($ch);

//     if (curl_errno($ch)) {
//         $error = curl_error($ch);
//         curl_close($ch);
//         return ['status' => 'ERROR', 'message' => $error];
//     }

//     curl_close($ch);
//     return json_decode($response, true);
// }



// public function test_waybill_batch_post()
// {
//     $samplePayload = [
//         "returnShipmentFlag" => false,
//         "Shipment" => [
//             "code" => "UC123456790",
//             "orderCode" => "123987653",
//             "SaleOrderCode" => "4554321",
//             "channelCode" => "23123",
//             "orderDate" => "2025-06-07 10:00:00",
//             "fullFilllmentTat" => "24",
//             "weight" => "500",
//             "length" => "10",
//             "breadth" => "10",
//             "height" => "10",
//             "numberOfBoxes" => "1",
//             "items" => [
//                 [
//                     "name" => "Product A",
//                     "description" => "This is a sample product",
//                     "quantity" => 1,
//                     "skuCode" => "SKU001",
//                     "itemPrice" => "1100"
//                 ]
//             ],
//             "source" => "unicommerce"
//         ],
//         "pickupAddressDetails" => [
//             "name" => "Warehouse",
//             "phone" => "9616412393",
//             "address1" => "G-357 Gamma2",
//             "pincode" => "201310",
//             "city" => "Greater Noida",
//             "state" => "UttarPradesh",
//             "country" => "India",
//             "stateCode" => "DL",
//             "countryCode" => "IN"
//         ],
//         "returnAddressDetails" => [
//             "name" => "Warehouse",
//             "phone" => "9123456780",
//             "address1" => "G-357 Alpha",
//             "pincode" => "201310",
//             "city" => "Greater Noida",
//             "state" => "Uttar Pradesh",
//             "country" => "India",
//             "stateCode" => "DL",
//             "countryCode" => "IN"
//         ],
//         "deliveryAddressDetails" => [
//             "name" => "Hariom",
//             "phone" => "7007723368",
//             "email" => "hariomshukla6414@gmail.com",
//             "address1" => "Gautam Budha University",
//             "pincode" => "201310",
//             "city" => "Greater Noida",
//             "state" => "Uttar Pradesh",
//             "country" => "India",
//             "stateCode" => "UP",
//             "countryCode" => "IN",
//             "gstin" => "29ABCDE1234F2Z5"
//         ],
//         "currencyCode" => "INR",
//         "paymentMode" => "prepaid",
//         "totalAmount" => "1100",
//         "collectableAmount" => "0"
//     ];

//     $results = [];

//     for ($i = 0; $i < 50; $i++) {
//         // Ensure unique orderCode and Shipment code for each iteration
//         $payload = $samplePayload;
//         $payload['Shipment']['orderCode'] = "123987653_" . $i;
//         $payload['Shipment']['code'] = "UC123456790_" . $i;

//         $response = $this->runWaybillGeneration($payload);
//         $results[] = $response;
//     }

//     header('Content-Type: application/json');
//     echo json_encode($results, JSON_PRETTY_PRINT);
// }





}
