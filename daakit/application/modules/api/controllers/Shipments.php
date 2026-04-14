<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';

class Shipments extends RestController
{
    var $account_id = false;
    var $method_name = "";

    public function __construct()
    {
        parent::__construct('rest_api');
        $this->validateAPIToken();
        $this->load->library('orders_lib');
        $this->load->library('shipping_lib');
        $this->load->library('ndr_lib');
    }

    private function validateAPIToken()
    {
        $this->load->library('jwt_lib');

        try {
            $api_data = $this->jwt_lib->validateAPI();
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

    function index_post()
    {
        $this->load->library('warehouse_lib');

        $this->load->library('courier_lib');

        $this->load->library('autoload_lib_sellers');

        $input_json = $this->input->raw_input_stream;

        $input_data = json_decode($input_json, true);

        do_action('log.create', 'API', [
            'action' => 'api_order_request',
            'ref_id' => !empty($input_data['orderNumber']) ? $input_data['orderNumber'] : '',
            'user_id' => $this->account_id,
            'data' => $input_data
        ]);
        $this->form_validation->set_data($input_data);

        $this->form_validation->set_message('required', '%s is required');

        $config = array(
            array(
                'field' => 'consignee[consigneeName]',
                'label' => 'Consignee name',
                'rules' => 'trim|required|min_length[1]|max_length[200]',
            ),
            array(
                'field' => 'consignee[company_name]',
                'label' => 'Consignee Company',
                'rules' => 'trim|min_length[1]|max_length[200]',
            ),
            array(
                'field' => 'consignee[consigneeAddress]',
                'label' => 'Consignee Address',
                'rules' => 'trim|required|min_length[2]|max_length[500]',
            ),
            array(
                'field' => 'consignee[consigneeAddress2]',
                'label' => 'Consignee Address 2',
                'rules' => 'trim|min_length[1]|max_length[200]',
            ),
            array(
                'field' => 'consignee[consigneeCity]',
                'label' => 'Consignee City',
                'rules' => 'trim|required|min_length[1]|max_length[40]',
            ),
            array(
                'field' => 'consignee[consigneeState]',
                'label' => 'Consignee State',
                'rules' => 'trim|required|min_length[1]|max_length[40]',
            ),
            array(
                'field' => 'consignee[consigneePincode]',
                'label' => 'Consignee Pincode',
                'rules' => 'trim|required|exact_length[6]|integer',
            ),
            array(
                'field' => 'consignee[consigneePhone]',
                'label' => 'Consignee Phone',
                'rules' => 'trim|required|exact_length[10]|integer',
            ),
            array(
                'field' => 'consignee[consigneeEmail]',
                'label' => 'Consignee Email',
                'rules' => 'trim|valid_email|max_length[255]',
            ),

            //order details

            array(
                'field' => 'orderNumber',
                'label' => 'Order Number',
                'rules' => 'trim|required|min_length[1]|max_length[30]',
            ),
            array(
                'field' => 'paymentType',
                'label' => 'Payment Type',
                'rules' => 'trim|required|in_list[cod,prepaid,reverse]',
            ),
            array(
                'field' => 'packageWeight',
                'label' => 'Package Weight',
                'rules' => 'trim|integer|greater_than_equal_to[0]',
            ),
            array(
                'field' => 'packageLength',
                'label' => 'Package Length',
                'rules' => 'trim|numeric|greater_than_equal_to[0]',
            ),
            array(
                'field' => 'packageBreadth',
                'label' => 'Package Breadth',
                'rules' => 'trim|numeric|greater_than_equal_to[0]',
            ),
            array(
                'field' => 'packageHeight',
                'label' => 'Package Height',
                'rules' => 'trim|numeric|greater_than_equal_to[0]',
            ),
            array(
                'field' => 'shippingCharges',
                'label' => 'Shipping Charges',
                'rules' => 'trim|numeric|greater_than_equal_to[0]',
            ),
            array(
                'field' => 'codCharges',
                'label' => 'COD Charges',
                'rules' => 'trim|numeric|greater_than_equal_to[0]',
            ),
            array(
                'field' => 'discount',
                'label' => 'Discount',
                'rules' => 'trim|numeric|greater_than_equal_to[0]',
            ),
            array(
                'field' => 'orderAmount',
                'label' => 'Order Total',
                'rules' => 'trim|required|numeric|greater_than[0]',
            ),
            array(
                'field' => 'collectableAmount',
                'label' => 'Collectible Amount',
                'rules' => 'trim|numeric',
            ),

            array(
                'field' => 'courierId',
                'label' => 'Courier ID',
                'rules' => 'trim|numeric|greater_than[0]',
            ),

            array(
                'field' => 'orderUniqueId',
                'label' => 'Order Unique ID',
                'rules' => 'trim|alpha_numeric|min_length[3]',
            ),

            //warehoue details

            array(
                'field' => 'pickupWarehouseId',
                'label' => 'Pickup Warehouse',
                'rules' => 'trim|required|integer',
            ),

            array(
                'field' => 'rtoWarehouseId',
                'label' => 'RTO Warehouse',
                'rules' => 'trim|required|integer',
            ),

            //order items
            array(
                'field' => 'orderItems[]',
                'label' => 'Order Items',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'isRequestAutoPickup',
                'label' => 'Request auto pickup',
                'rules' => 'trim|in_list[yes,no]',
            ),
            //insurance details
            array(
                'field' => 'isInsurance',
                'label' => 'Insurance',
                'rules' => 'trim|in_list[0,1]',
            ),
            array(
                'field' => 'tagIfAny',
                'label' => 'tags',
                'rules' => 'trim|max_length[200]',
            ),
            array(
                'field' => 'labelFormat',
                'label' => 'Label Format',
                'rules' => 'trim|in_list[thermal,standard]'
            ),
        );

        $qccheck = 0;
        $return_reason = (isset($input_data['return_reason'])) ? trim($input_data['return_reason']) : '';
        if ((!empty(($input_data['paymentType'])) && $input_data['paymentType'] == 'reverse') && (!empty(($input_data['qccheck'])))) {
            $qccheck = $input_data['qccheck'];
            if (!is_int($qccheck)) {
                do_action('log.create', 'API', [
                    'ref_id' => 'shipment2_user_' . $this->account_id . 'order' .  (!empty($input_data['order_number']) ? $input_data['order_number'] : '') . '_response',
                    'action' => 'Create Order Response',
                    'action_by' => $this->account_id,
                    'source' => 'shipment2 Create Shipment',
                    'data' => [
                        'success' => false,
                        "code"    => 404,
                        'message' => 'The Qccheck field must contain an integer'
                    ]
                ]);

                $this->response([
                    'status' => false,
                    'message' => 'The Qccheck field must contain an integer',
                ], 404);
            }
            $qc_check_rules = array(
                array(
                    'field' => 'qccheck',
                    'label' => 'Qccheck',
                    'rules' => 'trim|integer|in_list[0,1]',
                )
            );
            $qc_rules = array();
            if (!empty($input_data['qccheck']) && $input_data['qccheck'] == 1) {
                $qc_rules = array(
                    array(
                        'field' => 'product_usage',
                        'label' => 'Product Usage',
                        'rules' => 'trim|required|integer|in_list[0,1]',
                    ),
                    array(
                        'field' => 'product_damage',
                        'label' => 'Product Damage',
                        'rules' => 'trim|required|integer|in_list[0,1]',
                    ),
                    array(
                        'field' => 'brandname',
                        'label' => 'Brand Name',
                        'rules' => 'trim|required|integer|in_list[0,1]',
                    ),
                    array(
                        'field' => 'productsize',
                        'label' => 'Product Size',
                        'rules' => 'trim|required|integer|in_list[0,1]',
                    ),
                    array(
                        'field' => 'productcolor',
                        'label' => 'Product Color',
                        'rules' => 'trim|required|integer|in_list[0,1]',
                    ),

                    array(
                        'field' => 'order_category_id',
                        'label' => 'Order Category Id',
                        'rules' => 'trim|integer|greater_than[0]',
                    ),
                    array(
                        'field' => 'return_reason',
                        'label' => 'Order Return Reason',
                        'rules' => 'trim',
                    )
                );
            }
            $qc_rules = array_merge($qc_rules, $qc_check_rules);
            $config = array_merge($config, $qc_rules);
            $order_category_id = isset($input_data['order_category_id']) ? trim($input_data['order_category_id']) : '';
            $product_usage = isset($input_data['product_usage']) ? trim($input_data['product_usage']) : '';
            $product_damage = isset($input_data['product_damage']) ? trim($input_data['product_damage']) : '';
            $product_brandname = isset($input_data['brandname']) ? trim($input_data['brandname']) : '';
            $brandnametype = "";
            if ($product_brandname == '1') {
                $brandnametype = isset($input_data['brandnametype']) ? trim($input_data['brandnametype']) : '';
                $config[] = array(
                    'field' => 'brandnametype',
                    'label' => 'Brand Name type',
                    'rules' => 'trim|required'
                );
            }

            $product_productsize = isset($input_data['productsize']) ? trim($input_data['productsize']) : '';
            $productsizetype = "";
            if ($product_productsize == '1') {
                $productsizetype = isset($input_data['productsizetype']) ? trim($input_data['productsizetype']) : '';
                $config[] = array(
                    'field' => 'productsizetype',
                    'label' => 'Product Size type',
                    'rules' => 'trim|required'
                );
            }

            $product_productcolor = isset($input_data['productcolor']) ? trim($input_data['productcolor']) : '';
            $productcolourtype = "";
            if ($product_productcolor == '1') {
                $productcolourtype = isset($input_data['productcolourtype']) ? trim($input_data['productcolourtype']) : '';
                $config[] = array(
                    'field' => 'productcolourtype',
                    'label' => 'Product Colour type',
                    'rules' => 'trim|required'
                );
            }


            if ((!empty($input_data['qccheck']) && $input_data['qccheck'] == 1) && trim($input_data['uploadedimage']) == '' && trim($input_data['uploadedimage_2']) == '' && trim($input_data['uploadedimage_3']) == '' && trim($input_data['uploadedimage_4']) == '') {
                $config[] = array(
                    'field' => 'uploadedimage',
                    'label' => 'Atleast One Product Image',
                    'rules' => 'trim|required',
                );
            }


            $product_img_1 = trim($input_data['uploadedimage']);
            $product_img_2 = trim($input_data['uploadedimage_2']);
            $product_img_3 = trim($input_data['uploadedimage_3']);
            $product_img_4 = trim($input_data['uploadedimage_4']);
        } else {
            $order_category_id = '';
            $product_usage = '';
            $product_damage = '';
            $product_brandname = '';
            $brandnametype = '';
            $product_productsize = '';
            $productsizetype = '';
            $product_productcolor = '';
            $productcolourtype = '';
            $product_img_1 = '';
            $product_img_2 = '';
            $product_img_3 = '';
            $product_img_4 = '';
        }
        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->response([
                'status' => false,
                'message' => strip_tags(validation_errors())
            ], 404);
        }
        if (empty($input_data['orderItems']) || !is_array($input_data['orderItems'])) {
            $this->response([
                'status' => false,
                'message' => 'Order Items are required',
            ], 404);
        }

        if (($qccheck == 1)) {
            if (!empty($input_data['order_items']['name'])) {
                $input_data['order_items'][0]['name'] = $input_data['order_items']['name'];
                unset($input_data['order_items']['name']);
            }
            if (!empty($input_data['order_items']['price'])) {
                $input_data['order_items'][0]['price'] = $input_data['order_items']['price'];
                unset($input_data['order_items']['price']);
            }
            if (!empty($input_data['order_items']['sku'])) {
                $input_data['order_items'][0]['sku'] = $input_data['order_items']['sku'];
                unset($input_data['order_items']['sku']);
            }
            if (!empty($input_data['order_items']['qty'])) {
                unset($input_data['order_items']['qty']);
            }
        }
        foreach ($input_data['orderItems'] as  $p_key => $product) {
            if (empty($product['orderItemName'])) {
                $this->response([
                    'status' => false,
                    'message' => 'Item name is required',
                ], 404);
            }
            if ($qccheck == 0) {
                if (empty($product['orderItemQty']) || !is_numeric($product['orderItemQty'])) {
                    $this->response([
                        'status' => false,
                        'message' => 'Item qty is required',
                    ], 404); 
                }
                $input_data['orderItems'][$p_key]['orderItemQty'] = (int) $product['orderItemQty'];
            }

            $str = trim($product['orderItemQty']);
            if (empty($str)) {
                $this->response([
                    'status' => false,
                    'message' => 'Item qty should be positive integer value.',
                ], 404);
            };

            if (!empty($product['price']) && !is_numeric($product['orderItemPrice'])) {
                $this->response([
                    'status' => false,
                    'message' => 'The Item Price must contain only number.',
                ], 404);
            }
            $input_data['orderItems'][$p_key]['orderItemPrice'] = isset($product['orderItemPrice']) ? (float) $product['orderItemPrice'] : 0;
        }
        if (($qccheck == 1) &&  (count($input_data['orderItemPrice']) > 1)) {
            $this->response([
                'status' => false,
                'message' => 'Multiple product are not allowed for reverse qc',
            ], 404);
        }
        if (str_contains($input_data['packageLength'], '.') || str_contains($input_data['packageBreadth'], '.') || str_contains($input_data['packageHeight'], '.')) {
            $fieldArray = ['packageLength', 'packageBreadth', 'packageHeight'];
            foreach ($fieldArray as $key => $value) {
                $dimensionaArray = explode('.', $input_data[$value]);
                if (isset($dimensionaArray[1]) && strlen($dimensionaArray[1]) > 2) {
                    $input_data[$value] = number_format($input_data[$value], 2, '.', '');
                }
            }
        }
        $this->load->library('wallet_lib');
        if (!$this->wallet_lib->checkUserCanShip($this->account_id)) {
            $this->response([
                'status' => false,
                'message' => 'Availabe balance is low.',
            ], 404);
        }

        if (!empty($input_data['packageWeight']) && ($input_data['packageWeight'] > 50000)) {
            $this->response([
                'status' => false,
                'message' => 'Package weight should be less than 50 KG.',
            ], 404);
        }
        if (!empty($input_data['packageLength']) && !empty($input_data['packageBreadth']) && !empty($input_data['packageBreadth'])) {
            $vol_weight = round(($input_data['packageLength'] * $input_data['packageBreadth'] * $input_data['packageHeight']) / 5000, 3);
            if ($vol_weight > 50) {
                $this->response([
                    'status' => false,
                    'message' => 'Volumetric weight should be less than 50 KG.',
                ], 404);
            }
        }
        //*********Check duplicate order for seller start by Deep Rana ********/
        if (!empty($input_data['orderUniqueId'])) {
            $exitReqData = $this->orders_lib->checkOrderRequest($this->account_id, $input_data['orderUniqueId']);
            if (!empty($exitReqData)) {
                if ($exitReqData->fulfillment_status != 'cancelled') {
                    $shipment = $this->shipping_lib->getShipmentByOrderID($exitReqData->id);
                    if (!empty($shipment)) {
                        $this->load->library('user_lib');

                        $user = $this->user_lib->getByID($this->account_id);

                        $label_format = !empty($input_data['label_format']) ? trim($input_data['label_format']) : $user->label_format;

                        $label = $this->shipping_lib->generateLabel(array($shipment->shipping_id), $label_format, $this->account_id);
                
                        $manifest = '';
                        if (!empty($input_data['isRequestAutoPickup']) && $input_data['isRequestAutoPickup'] == "yes") {
                            $shipment_ids = array($shipment->shipping_id);
                            $pickup_ids = $this->shipping_lib->schedulePickup($this->account_id, $shipment_ids);
                            if (!empty($pickup_ids)) {
                                $this->load->library('pickups_lib');
                                $manifest = $this->pickups_lib->download_manifest($pickup_ids, $this->account_id);
                            }
                        }                        
                        $return = array(
                            'orderId' => (int)$exitReqData->id,
                            'shipmentId' => (int)$shipment->shipping_id,
                            'awbNumber' => $shipment->awb_number,
                            'courierId' => $shipment->courier_id,
                            'courierName' => $shipment->display_name,
                            'status' => 'booked',
                            'extraInfo' => $shipment->shipment_info_1,
                            'paymentType' => strtolower($shipment->order_payment_type),
                            'label' => $label,
                            'manifest' => $manifest
                        );
                        do_action('log.create', 'API', [
                            'action' => 'api_order_response',
                            'ref_id' => !empty($input_data['orderNumber']) ? $input_data['orderNumber'] : '',
                            'user_id' => $this->account_id,
                            'data' => [
                                'success' => true,
                                "code"    => 200,
                                'message' => $return
                            ]
                        ]);

                        $this->response([
                            'status' => true,
                            'data' => $return,
                        ], 200);
                    }
                }
            }
        }
        //*********Check duplicate order for seller end by Deep Rana ********/
        $order_data = array();
        $order_items =  isset($input_data['orderItems']) ? $input_data['orderItems'] : array();
        $consignee =  isset($input_data['consignee']) ? $input_data['consignee'] : array();
        $pickup_warehouse =  isset($input_data['pickup']) ? $input_data['pickup'] : array();
        $rto_warehouse =  isset($input_data['rto']) ? $input_data['rto'] : array();

        $pickup_warehouse_id =  isset($input_data['pickupWarehouseId']) ? $input_data['pickupWarehouseId'] : '0';
        $rto_warehouse_id =  isset($input_data['rtoWarehouseId']) ? $input_data['rtoWarehouseId'] : '0';

        if (strtolower($input_data['paymentType']) == 'cod') {
            $order_data['paymentType'] = 'COD';
        }

        $collectable_amount = 0;
        if (strtolower($input_data['paymentType']) == 'cod') {
            $order_total = isset($input_data['orderAmount']) ? $input_data['orderAmount'] : 0;
            $collectable_amount = (!empty($input_data['collectableAmount']) && !is_zero($input_data['collectableAmount']) ? $input_data['collectableAmount'] :  $order_total);
            if ($collectable_amount >= $order_total) {
                $collectable_amount = $order_total;
            }
        }
        $tags = (!empty($input_data['tagIfAny'])) ? $input_data['tagIfAny'] : '';
        $order_save = array(
            'user_id' => $this->account_id,
            'order_date' => time(),
            'order_no' => isset($input_data['orderNumber']) ? $input_data['orderNumber'] : '',
            'api_order_id' => isset($input_data['orderUniqueId']) ? $input_data['orderUniqueId'] : '',
            'order_amount' => isset($input_data['orderAmount']) ? $input_data['orderAmount'] : '0',
            'collectable_amount' => $collectable_amount,
            'shipping_charges' => isset($input_data['shippingCharges']) ? $input_data['shippingCharges'] : '0',
            'cod_charges' => isset($input_data['codCharges']) ? $input_data['codCharges'] : '0',
            'discount' => isset($input_data['discount']) ? $input_data['discount'] : '0',
            'order_payment_type' => isset($input_data['paymentType']) ? $input_data['paymentType'] : 'COD',
            'shipping_fname' => isset($consignee['consigneeName']) ? $this->removeSpecialChar($consignee['consigneeName']) : '',
            'shipping_lname' => '',
            'shipping_address' => isset($consignee['consigneeAddress']) ? $this->removeSpecialChar($consignee['consigneeAddress']) : '',
            'shipping_address_2' => isset($consignee['consigneeAddress2']) ? $this->removeSpecialChar($consignee['consigneeAddress2']) : '',
            'shipping_phone' => isset($consignee['consigneePhone']) ? $consignee['consigneePhone'] : '',
            'shipping_email' => isset($consignee['consigneeEmail']) ? $consignee['consigneeEmail'] : '',
            'shipping_city' => isset($consignee['consigneeCity']) ? $this->removeSpecialChar($consignee['consigneeCity']) : '',
            'shipping_state' => isset($consignee['consigneeState']) ? $this->removeSpecialChar($consignee['consigneeState']) : '',
            'shipping_country' => 'India',
            'shipping_zip' => isset($consignee['consigneePincode']) ? trim($consignee['consigneePincode']) : '',
            'package_weight' => isset($input_data['packageWeight']) ? $input_data['packageWeight'] : 500,
            'package_length' => isset($input_data['packageLength']) ? $input_data['packageLength'] : '',
            'package_breadth' => isset($input_data['packageBreadth']) ? $input_data['packageBreadth'] : '',
            'package_height' => isset($input_data['packageHeight']) ? $input_data['packageHeight'] : '',
            'order_source' => 'api',
            'qccheck' => (trim($qccheck) == '1') ? '1' : '0',
            'applied_tags' => trim($tags)
        );
        if (strtolower($input_data['paymentType']) == 'reverse') {
            $is_insurance=0;
            $order_save['order_no'] =   isset($input_data['orderNumber']) ? 'RO-' . '' . trim($input_data['orderNumber']) : '';
        }

        $order_id = $this->orders_lib->insertOrder($order_save);
        $this->orders_lib->deleteReverseQCOrderProduct($order_id);
        if (trim($qccheck) == 1) {
            $save_qc_product = array(
                'order_id' => $order_id,
                'order_category_id' => (!empty($rareRabbitProduct->category_id)) ? $rareRabbitProduct->category_id : $order_category_id,
                'product_usage' => $product_usage,
                'product_damage' => $product_damage,
                'brandname' => $product_brandname,
                'brand_name_text' => (empty($brandnametype) && !empty($rareRabbitProduct->brand)) ? $rareRabbitProduct->brand : $brandnametype,
                'productsize' => !empty($rareRabbitProduct->size) ? '1' : $product_productsize,
                'product_size_text' => !empty($rareRabbitProduct->size) ? $rareRabbitProduct->size : $productsizetype,
                'productcolor' => !empty($rareRabbitProduct->color) ? '1' : $product_productcolor,
                'product_color_text ' => !empty($rareRabbitProduct->color) ? $rareRabbitProduct->color : $productcolourtype,
                'return_reason' => $return_reason,
                'product_img_1' => $product_img_1,
                'product_img_2' => $product_img_2,
                'product_img_3' => $product_img_3,
                'product_img_4' => $product_img_4,
            );

            $this->orders_lib->insertReverseQCProduct($save_qc_product);
        }

        foreach ($order_items as $single_product) {
            if (trim($qccheck) == 1) {
                $single_product['qty'] = 1;
            }
            $product_save = array(
                'order_id' => $order_id,
                'user_id' => $this->account_id,
                'product_name' => $this->removeSpecialChar($single_product['orderItemName']),
                'product_qty' => $single_product['orderItemQty'],
                'product_price' => $single_product['orderItemPrice'],
                'product_sku' => !empty($single_product['orderItemSku']) ? $single_product['orderItemSku'] : '',
            );
            $this->orders_lib->insertProduct($product_save);
        }
        if (!empty($input_data['isShipmentCreated']) && $input_data['isShipmentCreated'] == 'no') {
            $this->load->library('notification_lib');
            $this->notification_lib->sendNotification(null, 'new', $order_id);
            $return = array(
                'orderId' => $order_id,
                'orderStatus' => 'New',
                'paymentType' => isset($input_data['paymentType']) ? $input_data['paymentType'] : 'COD',
            );
            
            $this->response([
                'status' => true,
                'data' => $return,
            ], 200);
        }
        $is_insurance = isset(($input_data['isInsurance'])) ? $input_data['isInsurance'] : 0;
        $essential_order = isset(($input_data['essentialOrder'])) ? $input_data['essentialOrder'] : 0;
        $dg_order = isset(($input_data['isDangerous'])) ? $input_data['isDangerous'] : 0;
        $tags = (!empty($input_data['tags'])) ? $input_data['tags'] : '';
        $input_data['courierId']=(empty($input_data['courierId'])) ? "autoship" : $input_data['courierId'];
        $awb_data = $this->orders_lib->shipAPIOrder($order_id, $this->account_id, $pickup_warehouse_id, $rto_warehouse_id, $input_data['courierId'], $essential_order, $dg_order, $is_insurance,true, $tags);

        if (!$awb_data) {
            $this->orders_lib->updateFulfillmentStatus($order_id, 'cancelled');
            $this->response([
                'status' => false,
                'message' => $this->orders_lib->get_error(),
            ], 404);
        }

        $shipment = $this->shipping_lib->getShipmentByID($awb_data['shipment_id']);

        $this->load->library('user_lib');

        $user = $this->user_lib->getByID($this->account_id);

        $label_format = !empty($input_data['labelFormat']) ? trim($input_data['labelFormat']) : $user->label_format;

        $label = $this->shipping_lib->generateLabel(array($shipment->shipping_id), $label_format, $this->account_id);

        $manifest = '';
        if (!empty($input_data['isRequestAutoPickup']) && $input_data['isRequestAutoPickup'] == "yes") {
            $shipment_ids = array($shipment->shipping_id);
            $pickup_ids = $this->shipping_lib->schedulePickup($this->account_id, $shipment_ids);
            if (!empty($pickup_ids)) {
                $this->load->library('pickups_lib');
                $manifest = $this->pickups_lib->download_manifest($pickup_ids, $this->account_id);
            }
        }


        $enable_not = $this->orders_lib->getEnable_custom_order($this->account_id); //order_payment_type
        if(!empty($enable_not) && $enable_not[0]->custom_order_confirm=='1' && strtolower($input_data['paymentType']) == 'cod' )
        {
          // do_action('whatsapp_neworder.message', $order_id); 

          // $this->load->library('Whatsappengage_charges');
          // $this->whatsappengage_charges->deductCharges('order', $this->user->account_id);
           //do_action('whatsapp_deduction.update', $this->user->account_id); 

           
          // $this->load->library('whatsappengage_lib');
          // $this->whatsappengage_lib->create_order($order_id); 
        }

        $return = array(
            'orderId' => $order_id,
            'shipmentId' => (int)$shipment->shipping_id,
            'awbNumber' => $shipment->awb_number,
            'courierId' => $shipment->courier_id,
            'courierName' => $shipment->display_name,
            'status' => $shipment->ship_status,
            'extraInfo' => $shipment->shipment_info_1,
            'paymentType' => strtolower($shipment->order_payment_type),
            'label' => $label,
            'manifest' => $manifest
        );

        do_action('log.create', 'API', [
            'action' => 'api_order_response',
            'ref_id' => !empty($input_data['orderNumber']) ? $input_data['orderNumber'] : '',
            'user_id' => $this->account_id,
            'data' => [
                'success' => true,
                "code"    => 200,
                'message' => $return
            ]
        ]);

        $this->response([
            'status' => true,
            'data' => $return,
        ], 200);
    }
    
    function createwarehouse_post()
    {
        $this->load->library('warehouse_lib');

        $this->load->library('autoload_lib_sellers');

        $input_json = $this->input->raw_input_stream;

        $input_data = json_decode($input_json, true);

        $this->form_validation->set_data($input_data);

        $this->form_validation->set_message('required', '%s is required');

        $config = array(
            //pickup warehoue details
            array(
                'field' => 'pickup[pickupWarehouseName]',
                'label' => 'Pickup Warehouse Name',
                'rules' => 'trim|required|min_length[3]|max_length[50]',
            ),
            array(
                'field' => 'pickup[pickupName]',
                'label' => 'Pickup Contact Name',
                'rules' => 'trim|required|min_length[1]|max_length[200]',
            ),
            array(
                'field' => 'pickup[pickupAddress]',
                'label' => 'Pickup Address',
                'rules' => 'trim|required|min_length[2]|max_length[500]',
            ),
            array(
                'field' => 'pickup[pickupAddress2]',
                'label' => 'Pickup Address 2',
                'rules' => 'trim|min_length[1]|max_length[200]',
            ),
            array(
                'field' => 'pickup[pickupPincode]',
                'label' => 'Pickup Pincode',
                'rules' => 'trim|required|exact_length[6]|integer',
            ),
            array(
                'field' => 'pickup[pickupPhone]',
                'label' => 'Pickup Phone',
                'rules' => 'trim|required|exact_length[10]|integer',
            ),
            array(
                'field' => 'pickup[pickGstNumber]',
                'label' => 'GST Number',
                'rules' => 'trim|exact_length[15]|alpha_numeric',
            )
        );

        $rto_different = false;
        if (!empty($input_data['isRtoDifferent']) && $input_data['isRtoDifferent'] == 'yes') {
            $rto_different = true;
            $rto_rules = array( //rto details
                array(
                    'field' => 'rto[rtoWarehouseName]',
                    'label' => 'RTO Warehouse Name',
                    'rules' => 'trim|required|min_length[3]|max_length[50]',
                ),
                array(
                    'field' => 'rto[rtoName]',
                    'label' => 'RTO Contact Name',
                    'rules' => 'trim|required|min_length[1]|max_length[200]',
                ),
                array(
                    'field' => 'rto[rtoAddress]',
                    'label' => 'RTO Address',
                    'rules' => 'trim|required|min_length[2]|max_length[500]',
                ),
                array(
                    'field' => 'rto[rtoAddress2]',
                    'label' => 'RTO Address 2',
                    'rules' => 'trim|min_length[1]|max_length[200]',
                ),
                array(
                    'field' => 'rto[rtoPincode]',
                    'label' => 'RTO Pincode',
                    'rules' => 'trim|required|exact_length[6]|integer',
                ),
                array(
                    'field' => 'rto[rtoPhone]',
                    'label' => 'RTO Phone',
                    'rules' => 'trim|required|exact_length[10]|integer',
                ),
                array(
                    'field' => 'rto[rtoGstNumber]',
                    'label' => 'RTO GST Number',
                    'rules' => 'trim|exact_length[15]|alpha_numeric',
                ),
            );

            $config = array_merge($config, $rto_rules);
        }
        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->response([
                'status' => false,
                'message' => strip_tags(validation_errors())
            ], 404);
        }
        $pickup_warehouse =  isset($input_data['pickup']) ? $input_data['pickup'] : array();
        $rto_warehouse =  isset($input_data['rto']) ? $input_data['rto'] : array();
        if(empty($pickup_warehouse)){
            $this->response([
                'status' => false,
                'message' => 'Invalid pickup format.'
            ], 404);
        }
        $this->load->library('pincode_lib');
        $pin_code = $this->pincode_lib->get_citystate($pickup_warehouse['pickupPincode']);
        if (empty($pin_code)) {
            $this->response([
                'status' => false,
                'message' => 'Invalid pickup pincode.'
            ], 404);
        }

        $pickup_warehouse=array( 
            "warehouse_name"=>$pickup_warehouse['pickupWarehouseName'],
            "name"=>$pickup_warehouse['pickupName'],
            "address"=>$pickup_warehouse['pickupAddress'],
            "address_2"=>$pickup_warehouse['pickupAddress2'],
            "city"=> $pin_code->city??'',
            "state"=> $pin_code->state??'',
            "pincode"=>$pickup_warehouse['pickupPincode'],
            "phone"=>$pickup_warehouse['pickupPhone'],
            "gst_number"=>$pickup_warehouse['pickGstNumber']);

        // pr($pickup_warehouse,1);
        $pickup_warehouse_id  = $this->matchWarehouse($pickup_warehouse);
        if ($rto_different) {
            if(empty($rto_warehouse)){
                $this->response([
                    'status' => false,
                    'message' => 'Invalid rto format.'
                ], 404);
            }
            $this->load->library('pincode_lib');
            $pin_code = $this->pincode_lib->get_citystate($rto_warehouse['rtoPincode']);
            if (empty($pin_code)) {
                $this->response([
                    'status' => false,
                    'message' => 'Invalid rto pincode.'
                ], 404);
            }

            $rto_warehouse=array( 
                "warehouse_name"=>$rto_warehouse['rtoWarehouseName'],
                "name"=>$rto_warehouse['rtoName'],
                "address"=>$rto_warehouse['rtoAddress'],
                "address_2"=>$rto_warehouse['rtoAddress2'],
                "pincode"=>$rto_warehouse['rtoPincode'],
                "city"=> $pin_code->city??'',
                "state"=> $pin_code->state??'',
                "phone"=>$rto_warehouse['rtoPhone'],
                "gst_number"=>$rto_warehouse['rtoGstNumber']);
    
            $rto_warehouse_id  = $this->matchWarehouse($rto_warehouse);
        } else {
            $rto_warehouse_id =  $pickup_warehouse_id;
        }
        $this->response([
            'status' => true,
            'pickupWarehouseId' => (int) $pickup_warehouse_id,
            'rtoWarehouseId' => (int) $rto_warehouse_id,
        ], 200);
    }
    private function matchWarehouse($warehouse = array())
    {
        if (empty($warehouse['warehouse_name']))
            return false;

        //first check if existing warehouse by name
        
        $warehouse_c = md5(url_title_address(trim(strtolower($warehouse['warehouse_name']))."".trim(strtolower($warehouse['address']))."".trim(strtolower($warehouse['city']))."".trim(strtolower($warehouse['state']))."".trim($warehouse['pincode'])."".trim($warehouse['phone'])));

        $existing_warehouse = $this->warehouse_lib->getUserWarehouseByDetails($this->account_id, $warehouse_c);
  
        if ($existing_warehouse) {
            //match all keys with the posted data

            if ($existing_warehouse->warehouse_all_details == $warehouse_c)
                return $existing_warehouse->id;
        }

        //create warehosue using the details
        $save = array(
            'user_id' => $this->account_id,
            'name' => $warehouse['warehouse_name'],
            'contact_name' => $warehouse['name'],
            'phone' => $warehouse['phone'],
            'address_1' => $warehouse['address'],
            'address_2' => isset($warehouse['address_2']) ? $warehouse['address_2'] : '',
            'city' => $warehouse['city'],
            'state' => $warehouse['state'],
            'country' => 'India',
            'zip' => trim($warehouse['pincode']),
            'gst_number' => !empty($warehouse['gst_number']) ? $warehouse['gst_number'] : '',
            'warehouse_all_details' => $warehouse_c,
            'active' => '1'
        );

        $new_id = $this->warehouse_lib->create($save);
        $this->warehouse_lib->createUpdateWarehouseWithCourier($new_id);

        return $new_id;
    }

    function track_awb_get($awb = false)
    {
        $this->form_validation->set_data(array('awbNumbers' => $awb));
        $this->form_validation->set_message('required', '%s is required');
        $this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');

        $config = array(
            array(
                'field' => 'awbNumbers',
                'label' => 'AWB Number',
                'rules' => 'trim|alpha_dash|min_length[1]|max_length[20]|required',
            ),
        );

        $this->form_validation->set_rules($config);

        if (!$this->form_validation->run() && !empty(validation_errors())) {
            $this->response([
                'status' => false,
                'message' => strip_tags(validation_errors())
            ], 404);
        }

        $filters = array(
            'awb_number' => $awb,
            'user_id' => $this->account_id
        );

        $return = array();
        $results = $this->shipping_lib->getAPIShipments($filters);
        //pr($results,1);

        if (empty($results))
            $this->response([
                'status' => false,
                'message' => 'Record not found'
            ], 400);
        $this->load->library('courier_lib');
        $courier = $this->courier_lib->getByCourierid($results[0]['courierId']);
        $results[0]['courierName']=(!empty($courier)) ? $courier->name : '';
        $return = $results[0];
        if (!empty($return['awbNumber'])) {
            //attach tracking details to this shipment
            $this->load->library('tracking_lib');
            $tracking = $this->tracking_lib->getByAWB($return['awbNumber']);
            if (!empty($return['rtoAwb'])) {
                $rto_tracking = $this->tracking_lib->getByAWB($return['rtoAwb']);

                if (!empty($rto_tracking)) {
                    $tracking = array_merge($tracking, $rto_tracking);
                }
            }


            array_multisort(array_column($tracking, 'event_time'), SORT_DESC, $tracking);
    
            $this->load->config('cred_status');
            $courier_status = !empty($this->config->item('nimb_webhook_status')['smartship']) ? $this->config->item('nimb_webhook_status')['smartship'] : '';
            $ncourier_status = $this->config->item('nimb_webhook_status');
            $history = array();
            if (!empty($tracking)) {
                foreach ($tracking as $trk) {
                    //pr($tracking,1);
                    $status=false;
                    switch ($trk->ship_status) {
                        case 'pending pickup':
                            $status_code = 'PP';
                            break;
                        case 'in transit':
                            if($courier_status && array_key_exists($trk->status_code, $courier_status)) {
                                $status=true;
                                $history[] = array(
                                    'status_code' =>'PKD',
                                    'location' => $trk->location,
                                    'event_time' => date('Y-m-d H:i', $trk->event_time),
                                    'message' => 'Picked'
                                );
                                $history[] = array(
                                    'status_code' =>strtoupper($courier_status[$trk->status_code]),
                                    'location' => $trk->location,
                                    'event_time' => date('Y-m-d H:i', $trk->event_time),
                                    'message' => 'Shipped'
                                );
                                break;
                            }
                            else{   
                                $status_code = 'IT';
                                break;
                            }
                        case 'exception':
                            $status_code = 'EX';
                            break;
                        case 'out for delivery':
                            $status_code = 'OFD';
                            break;
                        case 'delivered':
                            $status_code = 'DL';
                            break;
                        case 'lost':
                            $status_code = 'LT';
                            break;
                        case 'damaged':
                            $status_code = 'DG';
                            break;
                        case 'rto':
                            $status_code = 'RT';
                            break;
                        case 'rto in transit':
                            $status_code = 'RT-IT';
                            break;
                        case 'rto lost':
                            $status_code = 'RT-LT';
                            break;
                        case 'rto damaged':
                            $status_code = 'RT-DG';
                            break;
                        case 'rto delivered':
                            $status_code = 'RT-DL';
                            break;
                        default:
                            $status_code = 'N/A';
                    }
                    $status_code = !empty($ncourier_status[strtolower($courier->display_name)][strtoupper($trk->status_code)]) ? $ncourier_status[strtolower($courier->display_name)][strtoupper($trk->status_code)] : $status_code;

                    if(!$status){
                        $history[] = array(
                            'statusCode' => strtoupper($status_code),
                            'location' => $trk->location,
                            'eventTime' => date('Y-m-d H:i', $trk->event_time),
                            'message' => $trk->message
                        );
                    }
                }
            }
            $return['history'] = $history;
        }
        $this->response([
            'status' => true,
            'data' => $return
        ], 200);
    }

    function tracking_awb_get($awb = false)
    {

        $this->form_validation->set_data(array('awb' => $awb));
        $this->form_validation->set_message('required', '%s is required');
        $this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');

        $config = array(
            array(
                'field' => 'awb',
                'label' => 'AWB Number',
                'rules' => 'trim|alpha_dash|min_length[1]|max_length[20]|required',
            ),
        );

        $this->form_validation->set_rules($config);

        if (!$this->form_validation->run() && !empty(validation_errors())) {
            $this->response([
                'status' => false,
                'message' => strip_tags(validation_errors())
            ], 404);
        }

        $filters = array(
            'awb_number' => $awb
        );

        $return = array();
        $results = $this->shipping_lib->getAPIShipments($filters);
        if (empty($results))
            $this->response([
                'status' => false,
                'message' => 'Record not found'
            ], 400);

        $return = $results[0];
        if (!empty($return['awb_number'])) {
            //attach tracking details to this shipment
            $this->load->library('tracking_lib');
            $tracking = $this->tracking_lib->getByAWB($return['awb_number']);
            if (!empty($return['rto_awb'])) {
                $rto_tracking = $this->tracking_lib->getByAWB($return['rto_awb']);

                if (!empty($rto_tracking)) {
                    $tracking = array_merge($tracking, $rto_tracking);
                }
            }


            array_multisort(array_column($tracking, 'event_time'), SORT_DESC, $tracking);

            $history = array();

            if (!empty($tracking)) {
                foreach ($tracking as $trk) {

                    switch ($trk->ship_status) {
                        case 'pending pickup':
                            $status_code = 'PP';
                            break;
                        case 'in transit':
                            $status_code = 'IT';
                            break;
                        case 'exception':
                            $status_code = 'EX';
                            break;
                        case 'out for delivery':
                            $status_code = 'OFD';
                            break;
                        case 'delivered':
                            $status_code = 'DL';
                            break;
                        case 'lost':
                            $status_code = 'LT';
                            break;
                        case 'damaged':
                            $status_code = 'DG';
                            break;
                        case 'rto':
                            $status_code = 'RT';
                            break;
                        case 'rto in transit':
                            $status_code = 'RT-IT';
                            break;
                        case 'rto lost':
                            $status_code = 'RT-LT';
                            break;
                        case 'rto damaged':
                            $status_code = 'RT-DG';
                            break;
                        case 'rto delivered':
                            $status_code = 'RT-DL';
                            break;
                        default:
                            $status_code = 'N/A';
                    }
                    $history[] = array(
                        'status_code' => $status_code,
                        'location' => $trk->location,
                        'event_time' => date('Y-m-d H:i', $trk->event_time),
                        'message' => $trk->message
                    );
                }
            }
            $return['history'] = $history;
        }

        $this->response([
            'status' => true,
            'data' => $return
        ], 200);
    }

    function manifest_awb_post()
    {


        $input_json = $this->input->raw_input_stream;

        $input_data = json_decode($input_json, true);


        $this->form_validation->set_data($input_data);

        $this->form_validation->set_message('required', '%s is required');
        $this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');


        $config = array(
            array(
                'field' => 'awbNumbers[]',
                'label' => 'AWB Numbers',
                'rules' => 'trim|required',
            ),
        );

        $this->form_validation->set_rules($config);

        if (!$this->form_validation->run() && !empty(validation_errors())) {
            $this->response([
                'status' => false,
                'message' => strip_tags(validation_errors())
            ], 404);
        }

        $awbs = $input_data['awbNumbers'];
        if (empty($awbs) || !is_array($awbs))
            $this->response([
                'status' => false,
                'message' => 'Please send valid awb.'
            ], 404);

        if (count($awbs) > 100)
            $this->response([
                'status' => false,
                'message' => 'Only 100 AWBs are allowed in one time'
            ], 404);


        //get awb records from DB
        $shipments = $this->shipping_lib->getByAWBsMultipleWithUserID($this->account_id, $awbs);

        if (empty($shipments))
            $this->response([
                'status' => false,
                'message' => 'No records found'
            ], 404);

        $shipment_ids = array_column($shipments, 'id');
        $pickup_ids = $this->shipping_lib->schedulePickup($this->account_id, $shipment_ids);

        if (empty($pickup_ids))
            $this->response([
                'status' => false,
                'message' => $this->shipping_lib->get_error(),
            ], 404);

        //now generate manifest for all these shipments
        $this->load->library('pickups_lib');

        $manifest = $this->pickups_lib->download_manifest(array($pickup_ids), $this->account_id);

        $this->response([
            'status' => true,
            'data' => $manifest,
        ], 200);
    }

    function cancel_awb_post()
    {

        $input_json = $this->input->raw_input_stream;

        $input_data = json_decode($input_json, true);


        $this->form_validation->set_data($input_data);


        $this->form_validation->set_message('required', '%s is required');
        $this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');


        $config = array(
            array(
                'field' => 'awbNumber',
                'label' => 'awb',
                'rules' => 'trim|required|alpha_dash',
            ),
        );

        $this->form_validation->set_rules($config);

        if (!$this->form_validation->run()) {
            $this->response([
                'status' => false,
                'message' => strip_tags(validation_errors())
            ], 404);
        }

        $awb = $input_data['awbNumber'];

        $shipment = $this->shipping_lib->getByAWB($awb, $this->account_id);
        if (empty($shipment))
            $this->response([
                'status' => false,
                'message' => 'No records found',
            ], 404);

        if (!$this->shipping_lib->cancelShipment($shipment->id, $this->account_id)) {
            $this->response([
                'status' => false,
                'message' => $this->shipping_lib->get_error(),
            ], 404);
        } else {
            $this->response([
                'status' => true,
                'message' => 'Shipment Cancelled',
            ], 200);
        }
    }

    function label_awb_post()
    {
        $this->load->library('orders_lib');
        $this->load->library('shipping_lib');

        $input_json = $this->input->raw_input_stream;
        $input_data = json_decode($input_json, true);
        $this->form_validation->set_data($input_data);

        $this->form_validation->set_message('required', '%s is required');
        $this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');

 
        $config = array(
            array(
                'field' => 'awbNumbers[]',
                'label' => 'awb',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'labelFormat',
                'label' => 'Label Format',
                'rules' => 'trim|in_list[thermal,standard]'
            ),
        );

        $this->form_validation->set_rules($config);

        if (!$this->form_validation->run()) {
            $this->response([
                'status' => false,
                'message' => strip_tags(validation_errors())
            ], 404);
        }      
        $awb_no =$input_data['awbNumbers'];
        if(count($awb_no) >= $this->config->item('bulk_awb_limit'))
        {
            $this->response([
                'status' => false,
                'message' => 'AWB Number should not more then '.$this->config->item('bulk_awb_limit'),
            ], 404);
            exit;
        }

        $this->load->library('user_lib');

        $user = $this->user_lib->getByID($this->account_id);
        $label_format = !empty(trim($input_data['labelFormat'])) ? trim($input_data['labelFormat']) : $user->label_format;

        if (!$returnLabelData = $this->shipping_lib->generateLabelawb($awb_no, $label_format, $this->account_id )) {
            $this->response([
                'status' => false,
                'data' => 'No data found.',
            ], 404);
        } else {
            $this->response([
                'status' => true,
                'data' => $returnLabelData,
            ], 200);
        }
    }

    function bulk_awb_tracking1_post()
    {

        $input_json = $this->input->raw_input_stream;

        $input_data = json_decode($input_json, true);

        $this->form_validation->set_data($input_data);


        $this->form_validation->set_message('required', '%s is required');
        $this->form_validation->set_message('alpha_numeric_spaces', 'Only Characters, Numbers & Spaces are  allowed in %s');


        $config = array(
            array(
                'field' => 'awb[]',
                'label' => 'awb',
                'rules' => 'trim|required',
            ),
        );

        $this->form_validation->set_rules($config);

        if (!$this->form_validation->run()) {
            $this->response([
                'status' => false,
                'message' => strip_tags(validation_errors())
            ], 404);
        }

        $awb_no = $input_data['awb'];

        if (count($awb_no) > $this->config->item('bulkawb_limit')) {
            $this->response([
                'status' => false,
                'message' => 'AWB Number should not more then ' . $this->config->item('bulkawb_limit'),
            ], 404);
            exit;
        }

      $track_data = array();

        foreach ($awb_no as $awb) {
            $filters = array(
                'awb_number' => $awb,
                'user_id' => $this->account_id
            );

            $return = array();

            $results = $this->shipping_lib->getAPIShipments($filters);
            if (empty($results))
                    continue;

            $return = $results[0];
            if (!empty($return['awb_number'])) {
                //attach tracking details to this shipment
                $this->load->library('tracking_lib');
                $tracking = $this->tracking_lib->getByAWB($return['awb_number']);
                if (!empty($return['rto_awb'])) {
                    $rto_tracking = $this->tracking_lib->getByAWB($return['rto_awb']);

                    if (!empty($rto_tracking)) {
                        $tracking = array_merge($tracking, $rto_tracking);
                    }
                }


                array_multisort(array_column($tracking, 'event_time'), SORT_DESC, $tracking);

                $history = array();

                if (!empty($tracking)) {
                    foreach ($tracking as $trk) {

                        switch ($trk->ship_status) {
                            case 'pending pickup':
                                $status_code = 'PP';
                                break;
                            case 'in transit':
                                $status_code = 'IT';
                                break;
                            case 'exception':
                                $status_code = 'EX';
                                break;
                            case 'out for delivery':
                                $status_code = 'OFD';
                                break;
                            case 'delivered':
                                $status_code = 'DL';
                                break;
                            case 'lost':
                                $status_code = 'LT';
                                break;
                            case 'damaged':
                                $status_code = 'DG';
                                break;
                            case 'rto':
                                $status_code = 'RT';
                                break;
                            case 'rto in transit':
                                $status_code = 'RT-IT';
                                break;
                            case 'rto lost':
                                $status_code = 'RT-LT';
                                break;
                            case 'rto damaged':
                                $status_code = 'RT-DG';
                                break;
                            case 'rto delivered':
                                $status_code = 'RT-DL';
                                break;
                            default:
                                $status_code = 'N/A';
                        }
                        $history[] = array(
                            'status_code' => $status_code,
                            'location' => $trk->location,
                            'event_time' => date('Y-m-d H:i', $trk->event_time),
                            'message' => $trk->message
                        );
                    }
                }
                $return['history'] = $history;
            }
            $track_data[] = $return;
        }

        $this->response([
            'status' => true,
            'data' => $track_data
        ], 200);
    }

    function reverseqc_post()
    {
        $this->load->library('warehouse_lib');
        $this->load->library('courier_lib');
        $this->load->library('autoload_lib_sellers');
        $input_json = $this->input->raw_input_stream;
        $input_data = json_decode($input_json, true);
    
        do_action('log.create', 'API', [
            'action' => 'api_order',
            'ref_id' => !empty($input_data['order_number']) ? $input_data['order_number'] : '',
            'user_id' => $this->account_id,
            'data' => $input_data
        ]);
 // request_auto_pickup
        $this->form_validation->set_data($input_data);
        $this->form_validation->set_message('required', '%s is required');

        $config = array(
            array(
                'field' => 'consignee[name]',
                'label' => 'Consignee name',
                'rules' => 'trim|required|min_length[1]|max_length[200]',
            ),
            array(
                'field' => 'consignee[company_name]',
                'label' => 'Consignee Company',
                'rules' => 'trim|min_length[1]|max_length[200]',
            ),
            array(
                'field' => 'consignee[address]',
                'label' => 'Consignee Address',
                'rules' => 'trim|required|min_length[5]|max_length[200]',
            ),
            array(
                'field' => 'consignee[address_2]',
                'label' => 'Consignee Address 2',
                'rules' => 'trim|min_length[1]|max_length[200]',
            ),
            array(
                'field' => 'consignee[city]',
                'label' => 'Consignee City',
                'rules' => 'trim|required|min_length[1]|max_length[40]|alpha_numeric_spaces',
            ),
            array(
                'field' => 'consignee[state]',
                'label' => 'Consignee State',
                'rules' => 'trim|required|min_length[1]|max_length[40]|alpha_numeric_spaces',
            ),
            array(
                'field' => 'consignee[pincode]',
                'label' => 'Consignee Pincode',
                'rules' => 'trim|required|exact_length[6]|integer',
            ),
            array(
                'field' => 'consignee[phone]',
                'label' => 'Consignee Phone',
                'rules' => 'trim|required|exact_length[10]|integer|greater_than[0]',
            ),

            //order details  order_amount

            array(
                'field' => 'order_number',
                'label' => 'Order Number',
                'rules' => 'trim|required|min_length[1]|max_length[20]',
            ),
            array(
                'field' => 'payment_type',
                'label' => 'Payment Type',
                'rules' => 'trim|required|in_list[reverse]',
            ),
            array(
                'field' => 'request_auto_pickup',
                'label' => 'Request auto pickup',
                'rules' => 'trim|in_list[yes,no]',
            ),
            array(
                'field' => 'package_weight',
                'label' => 'Package Weight',
                'rules' => 'trim|integer|greater_than_equal_to[0]',
            ),
            array(
                'field' => 'package_length',
                'label' => 'Package Length',
                'rules' => 'trim|integer|greater_than_equal_to[0]',
            ),
            array(
                'field' => 'package_breadth',
                'label' => 'Package Breadth',
                'rules' => 'trim|integer|greater_than_equal_to[0]',
            ),
            array(
                'field' => 'package_height',
                'label' => 'Package Height',
                'rules' => 'trim|integer|greater_than_equal_to[0]',
            ),
            
            array(
                'field' => 'order_amount',
                'label' => 'Order Amount',
                'rules' => 'trim|required|numeric|greater_than[0]',
            ),
           
            array(
                'field' => 'courier_id',
                'label' => 'Courier ID',
                'rules' => 'trim|required|integer|greater_than[0]',
            ),
            array(
                'field' => 'qccheck',
                'label' => 'Qccheck',
                'rules' => 'trim|required|integer|in_list[1]',
            ), 

            array(
                'field' => 'product_usage',
                'label' => 'Product Usage',
                'rules' => 'trim|required|integer|in_list[0,1]',
            ), 
            array(
                'field' => 'product_damage',
                'label' => 'Product Damage',
                'rules' => 'trim|required|integer|in_list[0,1]',
            ), 
            array(
                'field' => 'brandname',
                'label' => 'Brand Name',
                'rules' => 'trim|required|integer|in_list[0,1]',
            ), 
            array(
                'field' => 'productsize',
                'label' => 'Product Size',
                'rules' => 'trim|required|integer|in_list[0,1]',
            ), 
            array(
                'field' => 'productcolor',
                'label' => 'Product Color',
                'rules' => 'trim|required|integer|in_list[0,1]',
            ), 
            
            array(
                'field' => 'order_category_id',
                'label' => 'Order Category Id',
                'rules' => 'trim|required|integer|greater_than[0]',
            ), 
            //pickup warehoue details
            array(
                'field' => 'pickup[warehouse_name]',
                'label' => 'Pickup Warehouse Name',
                'rules' => 'trim|required|min_length[3]|max_length[20]|alpha_numeric_spaces',
            ),
            array(
                'field' => 'pickup[name]',
                'label' => 'Pickup Contact Name',
                'rules' => 'trim|required|min_length[1]|max_length[200]',
            ),
            array(
                'field' => 'pickup[address]',
                'label' => 'Pickup Address',
                'rules' => 'trim|required|min_length[5]|max_length[200]',
            ),
            array(
                'field' => 'pickup[address_2]',
                'label' => 'Pickup Address 2',
                'rules' => 'trim|min_length[1]|max_length[200]',
            ),
            array(
                'field' => 'pickup[city]',
                'label' => 'Pickup City',
                'rules' => 'trim|required|min_length[1]|max_length[40]|alpha_numeric_spaces',
            ),
            array(
                'field' => 'pickup[state]',
                'label' => 'Pickup State',
                'rules' => 'trim|required|min_length[1]|max_length[40]|alpha_numeric_spaces',
            ),
            array(
                'field' => 'pickup[pincode]',
                'label' => 'Pickup Pincode',
                'rules' => 'trim|required|exact_length[6]|integer',
            ),
            array(
                'field' => 'pickup[phone]',
                'label' => 'Pickup Phone',
                'rules' => 'trim|required|exact_length[10]|integer|greater_than[0]',
            ),
            array(
                'field' => 'pickup[gst_number]',
                'label' => 'GST Number',
                'rules' => 'trim|exact_length[15]|alpha_numeric',
            ),

         //order items

            array(
                'field' => 'order_items[name]',
                'label' => 'Order Name',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'order_items[price]',
                'label' => 'Order Price',
                'rules' => 'trim|numeric|greater_than[0]|required',
            ),
            // array(
            //     'field' => 'order_items[]',
            //     'label' => 'Order Items',
            //     'rules' => 'trim|required',
            // ),
            

          
        );

       
        if (trim($input_data['qccheck']) == '1') {
              $order_category_id = trim($input_data['order_category_id']);
              $product_usage = trim($input_data['product_usage']);
              $product_damage = trim($input_data['product_damage']);
        
            //   $config[] = array(
            //       'field' => 'product_qty',
            //       'label' => 'Product Quantity',
            //       'rules' => 'trim|required|in_list[1]',
            //   );
        
              $product_brandname = trim($input_data['brandname']);
              $brandnametype = "";
              if ($product_brandname == '1') {
                  $brandnametype = trim($input_data['brandnametype']);
                  $config[] = array(
                      'field' => 'brandnametype',
                      'label' => 'Brand Name type',
                      'rules' => 'trim|required'
                  );
              }
        
              $product_productsize = trim($input_data['productsize']);
              $productsizetype = "";
              if ($product_productsize == '1') {
                  $productsizetype = trim($input_data['productsizetype']);
                  $config[] = array(
                      'field' => 'productsizetype',
                      'label' => 'Product Size type',
                      'rules' => 'trim|required'
                  );
              }
        
              $product_productcolor = trim($input_data['productcolor']);
              $productcolourtype = "";
              if ($product_productcolor == '1') {
                  $productcolourtype = trim($input_data['productcolourtype']);
                  $config[] = array(
                      'field' => 'productcolourtype',
                      'label' => 'Product Colour type',
                      'rules' => 'trim|required'
                  );
              }
    
        
              if (trim($input_data['uploadedimage']) == '' && trim($input_data['uploadedimage_2']) == '' && trim($input_data['uploadedimage_3']) == '' && trim($input_data['uploadedimage_4']) == '') {
                  $config[] = array(
                      'field' => 'uploadedimage',
                      'label' => 'Atleast One Product Image',
                      'rules' => 'trim|required',
                  );
              }
              
              $product_img_1 = trim($input_data['uploadedimage']);
              $product_img_2 = trim($input_data['uploadedimage_2']);
              $product_img_3 = trim($input_data['uploadedimage_3']);
              $product_img_4 = trim($input_data['uploadedimage_4']);
            }  
            else {
                $order_category_id = '';
                $product_usage = '';
                $product_damage = '';
                $product_brandname = '';
                $brandnametype = '';
                $product_productsize = '';
                $productsizetype = '';
                $product_productcolor = '';
                $productcolourtype = '';
                $product_img_1 = '';
                $product_img_2 = '';
                $product_img_3 = '';
                $product_img_4 = '';
            }
    

        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {

            do_action('log.create', 'API', [
                'action' => 'api_order_response',
                'ref_id' => !empty($input_data['order_number']) ? $input_data['order_number'] : '',
                'user_id' => $this->account_id,
                'data' => [
                    'success' => false,
                    "code"    => 400,
                    'message' => strip_tags(validation_errors())
                ]
            ]);

            $this->response([
                'status' => false,
                'message' => strip_tags(validation_errors())
            ], 404);
        }

    $input_data['payment_type']=  trim($input_data['payment_type']); 

        if (empty($input_data['order_items']) || !is_array($input_data['order_items'])) {
            do_action('log.create', 'API', [
                'action' => 'api_order_response',
                'ref_id' => !empty($input_data['order_number']) ? $input_data['order_number'] : '',
                'user_id' => $this->account_id,
                'data' => [
                    'success' => false,
                    "code"    => 400,
                    'message' => 'Order Items are required'
                ]
            ]);

            $this->response([
                'status' => false,
                'message' => 'Order Items are required',
            ], 404);
        }
   
       

        $this->load->library('wallet_lib');

        if (!$this->wallet_lib->checkUserCanShip($this->account_id)) {
            do_action('log.create', 'API', [
                'action' => 'api_order_response',
                'ref_id' => !empty($input_data['order_number']) ? $input_data['order_number'] : '',
                'user_id' => $this->account_id,
                'data' => [
                    'success' => false,
                    "code"    => 400,
                    'message' => 'Wallet balance is low.'
                ]
            ]);

            $this->response([
                'status' => false,
                'message' => 'Wallet balance is low.',
            ], 404);
        }

        if (!empty($input_data['package_weight']) && ($input_data['package_weight'] > 50000)) {
            do_action('log.create', 'API', [
                'action' => 'api_order_response',
                'ref_id' => !empty($input_data['order_number']) ? $input_data['order_number'] : '',
                'user_id' => $this->account_id,
                'data' => [
                    'success' => false,
                    "code"    => 400,
                    'message' => 'Package weight should be less than 50 KG.'
                ]
            ]);

            $this->response([
                'status' => false,
                'message' => 'Package weight should be less than 50 KG.',
            ], 404);
        }

        if (empty($input_data['courier_id']))
            $input_data['courier_id'] = 'autoship';

        $order_data = array();
        $order_items =  isset($input_data['order_items']) ? $input_data['order_items'] : array();
        $consignee =  isset($input_data['consignee']) ? $input_data['consignee'] : array();
        $pickup_warehouse =  isset($input_data['pickup']) ? $input_data['pickup'] : array();
        $rto_warehouse =  isset($input_data['rto']) ? $input_data['rto'] : array();

        $pickup_warehouse_id  = $this->matchWarehouse($pickup_warehouse);
        if ($rto_different) {
            $rto_warehouse_id  = $this->matchWarehouse($rto_warehouse);
        } else {
            $rto_warehouse_id =  $pickup_warehouse_id;
        }

      
       

        $order_save = array(
            'user_id' => $this->account_id,
            'order_date' => time(),
            'order_id' =>   isset($input_data['order_number']) ? 'R-' . '' .trim($input_data['order_number']) : '',
            'order_amount' => isset($input_data['order_amount']) ? trim($input_data['order_amount']) : '0',
            'shipping_charges' => isset($input_data['shipping_charges']) ? trim($input_data['shipping_charges']) : '0',
            'cod_charges' => isset($input_data['cod_charges']) ? trim($input_data['cod_charges']) : '0',
            'discount' => isset($input_data['discount']) ? trim($input_data['discount']) : '0',
            'order_payment_type' => isset($input_data['payment_type']) ? trim($input_data['payment_type']) : 'COD',
            'shipping_fname' => isset($consignee['name']) ? trim($consignee['name']) : '',
            'shipping_lname' => '',
             'qccheck' =>'1',
            'shipping_address' => isset($consignee['address']) ? trim($consignee['address']) : '',
            'shipping_address_2' => isset($consignee['address_2']) ? trim($consignee['address_2']) : '',
            'shipping_phone' => isset($consignee['phone']) ? trim($consignee['phone']) : '',
            'shipping_city' => isset($consignee['city']) ? trim($consignee['city']) : '',
            'shipping_state' => isset($consignee['state']) ? trim($consignee['state']) : '',
            'shipping_country' => 'India',
            'shipping_zip' => isset($consignee['pincode']) ? trim($consignee['pincode']) : '',
            'package_weight' => isset($input_data['package_weight']) ? ((floor(trim($input_data['package_weight']) / 500) >= 1) ? trim($input_data['package_weight']) : 500) : 500,
            'package_length' => isset($input_data['package_length']) ? trim($input_data['package_length']) : '',
            'package_breadth' => isset($input_data['package_breadth']) ? trim($input_data['package_breadth']) : '',
            'package_height' => isset($input_data['package_height']) ? trim($input_data['package_height']) : '',
            'order_source' => 'api'
        );
//pr($order_save); die;
       
        $order_id = $this->orders_lib->insertOrder($order_save); 

        $this->orders_lib->deleteReverseQCOrderProduct($order_id);
        if (trim($input_data['qccheck']) == '1') {
            $save_qc_product = array(
                'order_id' => $order_id,
                'order_category_id' => $order_category_id,
                'product_usage' => $product_usage,
                'product_damage' => $product_damage,
                'brandname' => $product_brandname,
                'brand_name_text' => $brandnametype,
                'productsize' => $product_productsize,
                'product_size_text' => $productsizetype,
                'productcolor' => $product_productcolor,
                'product_color_text ' => $productcolourtype,
                'product_img_1' => $product_img_1,
                'product_img_2' => $product_img_2,
                'product_img_3' => $product_img_3,
                'product_img_4' => $product_img_4,
            );


            $this->orders_lib->insertReverseQCProduct($save_qc_product);
        }   
    
        if (!empty($order_items)) {
            $product_save = array(
                'order_id' => $order_id,
                'product_name' => trim($order_items['name']),
                'product_qty' => '1',
                'product_price' => trim($order_items['price']),
                'product_sku' => !empty($order_items['sku']) ? $order_items['sku'] : '',
            );
           

            $this->orders_lib->insertProduct($product_save);
        }
       
        $is_insurance = isset(($input_data['is_insurance'])) ? $input_data['is_insurance'] : 0;
        $essential_order = isset(($input_data['essential_order'])) ? $input_data['essential_order'] : 0;
        $dg_order = isset(($input_data['dg_order'])) ? $input_data['dg_order'] : 0;
        $tags = (!empty($input_data['tags'])) ? $input_data['tags'] : '';

     
        
        $awb_data = $this->orders_lib->shipAPIOrder($order_id, $this->account_id, $pickup_warehouse_id, $rto_warehouse_id, $input_data['courier_id'], $essential_order, $dg_order, $is_insurance,true, $tags);

     
        if (!$awb_data) {
            do_action('log.create', 'API', [
                'action' => 'api_order_response',
                'ref_id' => !empty($input_data['order_number']) ? $input_data['order_number'] : '',
                'user_id' => $this->account_id,
                'data' => [
                    'success' => false,
                    "code"    => 400,
                    'message' => $this->orders_lib->get_error()
                ]
            ]);

            $this->orders_lib->updateFulfillmentStatus($order_id, 'cancelled');
            $this->response([
                'status' => false,
                'message' => $this->orders_lib->get_error(),
            ], 404);
        }

        $shipment = $this->shipping_lib->getShipmentByID($awb_data['shipment_id']);

        $this->load->library('user_lib');

        $user = $this->user_lib->getByID($this->account_id);

        $label_format = !empty($input_data['label_format']) ? trim($input_data['label_format']) : $user->label_format;

        $label = $this->shipping_lib->generateLabel(array($shipment->shipping_id), $label_format, $this->account_id);

        $manifest = '';
        if (!empty($input_data['request_auto_pickup']) && $input_data['request_auto_pickup'] == "yes") {
            $shipment_ids = array($shipment->shipping_id);
            $pickup_ids = $this->shipping_lib->schedulePickup($this->account_id, $shipment_ids);
            if (!empty($pickup_ids)) {
                $this->load->library('pickups_lib');
                $manifest = $this->pickups_lib->download_manifest($pickup_ids, $this->account_id);
            }
        }

        if(!empty($shipment->shipment_info_1) && $shipment->courier_id == 137) {
            $shipment_info_1 = json_decode($shipment->shipment_info_1, 1);
            if(empty($shipment_info_1)) {
                $shipment->shipment_info_1 = $shipment->shipment_info_1.'"}';
                $shipment_info_1 = json_decode($shipment->shipment_info_1, 1);
            }

            $shipment->shipment_info_1 = $shipment_info_1['route_code'];
        }

        $return = array(
            'order_id' => $order_id,
            'shipment_id' => (int)$shipment->shipping_id,
            'awb_number' => $shipment->awb_number,
            'courier_id' => $shipment->courier_id,
            'courier_name' => $shipment->display_name,
            'status' => $shipment->ship_status,
            'additional_info' => $shipment->shipment_info_1,
            'payment_type' => strtolower($shipment->order_payment_type),
            'label' => $label,
            'manifest' => $manifest
        );

        do_action('log.create', 'API', [
            'action' => 'api_order_response',
            'ref_id' => !empty($input_data['order_number']) ? $input_data['order_number'] : '',
            'user_id' => $this->account_id,
            'data' => [
                'success' => true,
                "code"    => 200,
                'message' => $return
            ]
        ]);

        $this->response([
            'status' => true,
            'data' => $return,
        ], 200);
    }
    public function removeSpecialChar($str){
        return $str;
    }

    public function ndr_action_post()
{
    $input_json = $this->input->raw_input_stream;
    $input_data = json_decode($input_json, true);

    if (empty($input_data)) {
        return $this->response(['status' => false, 'message' => 'Invalid or empty JSON payload'], 400);
    }

    $this->form_validation->set_data($input_data);

  
    $config = [
        ['field' => 'awb_number', 'label' => 'AWB Number', 'rules' => 'trim|required'],
        ['field' => 'action', 'label' => 'Action', 'rules' => 'trim|required'],
        ['field' => 'remarks', 'label' => 'Remarks', 'rules' => 'trim|max_length[200]']
    ];

  
    switch (strtolower($input_data['action'])) {
        case 're-attempt':
            $config[] = ['field' => 're_attempt_date', 'label' => 'Re-Attempt Date', 'rules' => 'trim|required|regex_match[/^\d{4}-\d{2}-\d{2}$/]'];
            break;

        case 'change address':
            $config = array_merge($config, [
                ['field' => 'customer_details_name', 'label' => 'Customer Name', 'rules' => 'trim|required|max_length[50]'],
                ['field' => 'customer_details_address_1', 'label' => 'Customer Address 1', 'rules' => 'trim|required|min_length[10]|max_length[200]'],
                ['field' => 'customer_details_address_2', 'label' => 'Customer Address 2', 'rules' => 'trim|max_length[200]']
            ]);
            break;

        case 'change phone':
            $config[] = ['field' => 'customer_contact_phone', 'label' => 'Phone Number', 'rules' => 'trim|required|exact_length[10]|numeric'];
            break;
    }

    $this->form_validation->set_rules($config);

    if (!$this->form_validation->run()) {
        return $this->response([
            'status' => false,
            'message' => strip_tags(validation_errors())
        ], 400);
    }

   
    $awb_number = trim($input_data['awb_number']);
    $action = strtolower(trim($input_data['action']));
    $remarks = $input_data['remarks'] ?? '';

    $re_attempt_date = 0;
    if (!empty($input_data['re_attempt_date'])) {
        $dateInput = $input_data['re_attempt_date'];

        $dateObj = DateTime::createFromFormat('Y-m-d', $dateInput);
        if ($dateObj && $dateObj->format('Y-m-d') === $dateInput) {
            $re_attempt_date = strtotime($dateInput);
        } else {
            return $this->response(['status' => false, 'message' => 'Invalid Re-Attempt Date format. Use YYYY-MM-DD'], 400);
        }
    }

    $customer_details_name = $input_data['customer_details_name'] ?? '';
    $customer_details_address_1 = $input_data['customer_details_address_1'] ?? '';
    $customer_details_address_2 = $input_data['customer_details_address_2'] ?? '';
    $customer_contact_phone = $input_data['customer_contact_phone'] ?? '';

    
    $shipment = $this->shipping_lib->getByAWB($awb_number, false);
    if (empty($shipment)) {
        return $this->response(['status' => false, 'message' => 'Invalid AWB Number'], 404);
    }

   
    $ndr = $this->ndr_lib->getByShippingID($shipment->id);
    if (empty($ndr)) {
        return $this->response(['status' => false, 'message' => 'No NDR record found for this shipment'], 404);
    }

    $ndr_id = $ndr->id;

    
    $update = [
        'ndr_id' => $ndr_id,
        'action' => $action,
        'remarks' => $remarks,
        'source' => 'seller',
        're_attempt_date' => $re_attempt_date, 
        'customer_details_name' => $customer_details_name,
        'customer_details_address_1' => $customer_details_address_1,
        'customer_details_address_2' => $customer_details_address_2,
        'customer_contact_phone' => $customer_contact_phone,
    ];

   
    $action_id = $this->ndr_lib->Add_NDR_Action($update);
    if (!$action_id || !is_numeric($action_id)) {
        return $this->response(['status' => false, 'message' => 'Failed to add NDR action'], 500);
    }

    
    $save = [
        'last_action' => $action,
        'last_action_by' => 'seller',
        'latest_remarks' => $remarks,
        'last_event' => time(),
        'last_action_id' => $action_id
    ];

    if (!$this->ndr_lib->update($ndr_id, $save)) {
        return $this->response(['status' => false, 'message' => 'Failed to update NDR record'], 500);
    }

   
    $push_result = $this->ndr_lib->pushNdrActionToCourier($ndr_id);
    if (!$push_result) {
        $error_message = !empty($this->ndr_lib->error) ? $this->ndr_lib->error : 'Unknown error while pushing NDR.';
        return $this->response(['status' => false, 'message' => $error_message], 500);
    }

    return $this->response([
        'status' => true,
        'message' => 'Request submitted successfully',
    ], 200);
}
}
