<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Orders extends Admin_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('admin/orders_lib');
        $this->userHasAccess('orders');
    }

    function all($page = 1)
    {
        $per_page = $this->input->post('perPage');
        $page = $this->input->post('page') ?? 1;
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 50;
        else
            $limit = $per_page;

        $filter = $this->input->post('filter');
        $apply_filters = array();

        if (!empty($filter['order_type'])) {
            $apply_filters['order_type'] = $filter['order_type'];
        }

        $apply_filters['start_date'] = strtotime("today midnight -30 days");

        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;

        if (!empty($filter['order_ids'])) {
            $apply_filters['order_ids'] = array_map('trim', explode(',', $filter['order_ids']));
        }
        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }
        if (!empty($filter['search_query'])) {
            $apply_filters['search_query'] = $filter['search_query'];
        }
        if (!empty($filter['start_date'])) {
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        } else {
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);
        }
        if (!empty($filter['end_date'])) {
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        } else {
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);
        }
        if (!empty($filter['pay_method'])) {
            $apply_filters['pay_method'] = $filter['pay_method'];
        }
        if (!empty($filter['fulfillment'])) {
            $apply_filters['fulfillment'] = $filter['fulfillment'];
        }

        if ($this->restricted_permissions) {
            $apply_filters['account_manager_id'] = $this->user->user_id;
        }
       $current_url=current_url()."/".$page;
        $total_row = $this->orders_lib->countByUserID($apply_filters);
        $config = array(
            'base_url' => base_url('admin/orders/all'),
            'total_rows' => $total_row,
            'per_page' => $limit,
            'num_links' => '3',
            'use_page_numbers' => true,
            'reuse_query_string' => true,
            'cur_page' => $page,
            'cur_tag_open' => '<li class="paginate_button page-item active"><a class="page-link" href="' . current_url() . '">',
            'cur_tag_close' => '</a></li>',
            'num_tag_open' => '<li class="paginate_button page-item ">',
            'num_tag_close' => '</li>',
            'attributes' => array(
                'aria-controls' => 'example-multi',
                'tabindex' => '0',
                'class' => 'page-link',
            ),
            'next_link' => 'Next',
            'prev_link' => 'Prev',
        );
        $this->load->library("pagination");
        $this->pagination->initialize($config);
        $this->data["pagination"] = $this->pagination->create_links();
        $offset = $limit * ($page - 1);
        $this->data['total_records'] = $total_row;
        $this->data['limit'] = $limit;
        $this->data['offset'] = $offset;
        $this->data['filter'] = $filter;
        $orders = $this->orders_lib->fetchByUserID($limit, $offset, $apply_filters);
        $this->load->library('admin/user_lib');
        $seller_details = '';
        if (!empty($filter['seller_id']))
            $seller_details = $this->user_lib->getUserListFilter($filter['seller_id']);
        
        $this->data['users'] = $seller_details;
        $status_orders = array();
        $status_order_count = $this->orders_lib->countByUserIDStatusGrouped($apply_filters);
        if (!empty($status_order_count))
            foreach ($status_order_count as $status_count) {
                $status_orders[strtolower($status_count->fulfillment_status)] = $status_count->total_count;
            }
        $this->data['orders'] = $orders;
        $this->data['count_by_status'] = $status_orders;
        $this->layout('orders/index');
    }

    function view($id = false)
    {
        if (!$id)
            redirect('admin/orders/all', true);

        $order = $this->orders_lib->getByID($id);
        //pr($order);die;
        if (empty($order)) {
            $this->session->set_flashdata('error', 'Invalid Request');
            redirect('admin/orders/all', true);
        }

        $warehouse = false;

        //get shipping details
        $this->load->library('admin/shipping_lib');
        $shipping = $this->shipping_lib->getByOrderID($order->id);
        $courier = false;
        if (!empty($shipping)) {
            //get courioer details
            $this->load->library('courier_lib');
            $courier = $this->courier_lib->getByID($shipping->courier_id);
            //get warehouse details
            $this->load->library('warehouse_lib');
            if ($shipping->warehouse_id == '0') {
                $warehouse = $this->warehouse_lib->getUserWarehouse($order->user_id);
            } else {
                $warehouse = $this->warehouse_lib->getByID($shipping->warehouse_id);
            }
        }
        // get shippment history
        $shippinghistory = $this->shipping_lib->getByOrderhistory($order->id);

        //get seller details
        $sellerdetails = $this->orders_lib->getsellerbyid($order->user_id);
        $products = $this->orders_lib->getOrderProducts($order->id);

         // get product details
        $this->load->library('products_lib');
        if($order->fulfillment_status=='booked'){
           $result_data =   $this->products_lib->getProductDetailsbyOrder($order->id);
           if($result_data){
            if(((isset($result_data->weight))) && ((isset($result_data->length))) && ((isset($result_data->breadth))) && ((isset($result_data->height))) ){
                $order->seller_applied_weight =  $result_data->weight;
                $order->seller_applied_length =  $result_data->length;
                $order->seller_applied_breadth = $result_data->breadth;
                $order->seller_applied_height =  $result_data->height;
                $order->seller_applied_weight_status = "Applied";
            }
           }
           
        }else{
            if((!empty($products)) && (count($products)==1)){
                   $products_details_data = $this->products_lib->getOrderProductdetails($order->id,$products);
                   if(!empty($products_details_data)){
                        $order = $this->products_lib->getChargebleData($products_details_data,$order);
                        $order->seller_applied_weight_status = "Applicable";
                   }
            }  
        }

        $this->data['sellerdetails'] = $sellerdetails;
        $this->data['order'] = $order;
        $this->data['warehouse'] = $warehouse;
        $this->data['products'] = $products;
        $this->data['shiphistory'] = $shippinghistory;
        $this->data['shipping'] = $shipping;
        $this->data['courier'] = $courier;
        $this->layout('orders/view');
    }

    function exportCSV()
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', '2400');
        
        $filter = $this->input->get('filter');
        $apply_filters = array();

        if (!empty($filter['order_type'])) {
            $apply_filters['order_type'] = $filter['order_type'];
        }

        $apply_filters['start_date'] = (!empty($filter['start_date'])) ? strtotime(trim($filter['start_date']) . ' 00:00:00') : strtotime(date('Y-m-d 00:00:00'));
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;

        if (!empty($filter['order_ids'])) {
            $apply_filters['order_ids'] = array_map('trim', explode(',', $filter['order_ids']));
        }
        if (!empty($filter['seller_id'])) {
            $apply_filters['seller_id'] = $filter['seller_id'];
        }
        if (!empty($filter['product_name'])) {
            $apply_filters['product_name'] = $filter['product_name'];
        }

        if (!empty($filter['start_date'])) {
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        } else {
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);
        }
        
        if (!empty($filter['end_date'])) {
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        } else {
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);
        }

        if (!empty($filter['search_query'])) {
            $apply_filters['search_query'] = $filter['search_query'];
        }

        if (!empty($filter['pay_method'])) {
            $apply_filters['pay_method'] = $filter['pay_method'];
        }

        if (!empty($filter['fulfillment'])) {
            $apply_filters['fulfillment'] = $filter['fulfillment'];
        }

        if ($this->restricted_permissions) {
            $apply_filters['account_manager_id'] = $this->user->user_id;
        }

        $this->data['filter'] = $filter;
        $orders = $this->orders_lib->fetchByUserID(15000, 0, $apply_filters);

        $filename = 'Order_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');

        $header = array("Order Id", "Order Date", "Seller Name", "Product Name", "Payment", "Payment Type", "Customer Name", "Phone Number", "Email", "Address", "City", "State", "Zip Code", "Weight(GRAM)", "Status", "IVR Confirmation Status");

        $header = array_flip($header);
        if (!in_array('view_seller_detail', $this->data['user_details']->permissions)) {
                unset($header['Seller Name']);
        }

        if (!in_array('view_product_detail', $this->data['user_details']->permissions)) {
            unset($header['Product Name']);
        }

        if (!in_array('view_customer_detail', $this->data['user_details']->permissions)) {
            unset($header['Customer Name']);
            unset($header['Phone Number']);
            unset($header['Address']);
        }
        $header = array_flip($header);

      
        fputcsv($file, $header);
        $data = array();
        
        foreach ($orders as $order) {
            $data['order_id'] = $order->order_no;
            $data['order_date'] = date('Y-m-d', $order->order_date);
            $data['seller_name'] = ucwords($order->user_fname . ' ' . $order->user_lname);
            $data['products'] = $order->products;
            $data['order_amount'] =  $order->order_amount;
            $data['order_payment_type'] =  $order->order_payment_type;
            $data['customer_name'] = str_replace(';', ',', $order->shipping_fname . ' ' . $order->shipping_lname);
            $data['shipping_phone'] = $order->shipping_phone;
            $data['shipping_email'] = $order->shipping_email;
            $data['shipping_address'] = str_replace(';', ',', $order->shipping_address);
            $data['shipping_city'] = $order->shipping_city;
            $data['shipping_state'] = $order->shipping_state;
            $data['shipping_zip'] = $order->shipping_zip;
            $data['package_weight'] = $order->package_weight;
            $data['fulfillment_status'] = ($order->fulfillment_status == 'new') ? 'Not Booked' : $order->fulfillment_status;
            $data['ivr_status'] = $order->ivr_status;

            if (!in_array('view_seller_detail', $this->data['user_details']->permissions)) {
                unset($data['seller_name']);
            }

            if (!in_array('view_product_detail', $this->data['user_details']->permissions)) {
                unset($data['products']);
            }
    
            if (!in_array('view_customer_detail', $this->data['user_details']->permissions)) {
                unset($data['customer_name']);
                unset($data['shipping_phone']);
                unset($data['shipping_address']);
            }

            fputcsv($file, $data);
        }
        fclose($file);
        exit;
    }

    function get_delivery_info($order_id = false)
    {
        if (empty($order_id)) {
            $this->data['error'] = 'Invalid Request';
            $this->layout(true, 'json');
        }

        $order = $this->orders_lib->getByID($order_id);
        if (empty($order) || $order->user_id != $this->user->user_id) {
            $this->data['error'] = 'Invalid Request';
            $this->layout('orders/delivery_info', 'NONE');
        } else {
            //get warehouse details
            $this->load->library('warehouse_lib');
            $warehouse = $this->warehouse_lib->getUserWarehouse($order->user_id);

            if (empty($warehouse)) {
                $this->data['error'] = 'Warehouse Details Missing';
                $this->layout('orders/delivery_info', 'NONE');
            } else {

                //get order pin code
                $pin_code = $order->shipping_zip;

                //check pin code serviceblity
                $this->load->library('pincode_lib');

                $couriers = $this->pincode_lib->getPincodeService($pin_code, $method = false);
                $this->data['couriers'] = $couriers;
                $this->data['order'] = $order;
                //$services;
                $this->layout('orders/delivery_info', 'NONE');
            }
        }
    }

    function getBulkShipCouriers()
    {
        $this->load->library('courier_lib');
        //get warehouse details
        $this->load->library('warehouse_lib');
        $warehouse = $this->warehouse_lib->getUserWarehouse($this->user->user_id);

        if (empty($warehouse)) {
            $this->data['error'] = 'Warehouse Details Missing';
        } else {
            $couriers = $this->courier_lib->list_couriers(true);
            $this->data['couriers'] = $couriers;
            //$services;
        }
        $this->layout('orders/bulk_ship_couriers', 'NONE');
    }

    function ship()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'order_id',
                'label' => 'Order ID',
                'rules' => 'trim|required|min_length[1]|max_length[20]|numeric'
            ),
            array(
                'field' => 'courier_id',
                'label' => 'Courier ID',
                'rules' => 'trim|required|min_length[1]|max_length[6]|numeric'
            ),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $order_id = $this->input->post('order_id');
            $courier_id = $this->input->post('courier_id');
            //process order shipment

            $order = $this->orders_lib->getByID($order_id);

            if (empty($order) || $order->user_id != $this->user->user_id) {
                $this->data['json'] = array('error' => 'Invalid Request');
            } elseif ($order->fulfillment_status != 'new') {
                $this->data['json'] = array('error' => 'Order is already booked');
            } else {
                //process order for shipment
                if (!$this->orders_lib->processOrderShipment($order_id, $courier_id)) {
                    $this->data['json'] = array('error' => $this->orders_lib->get_error());
                } else {
                    $this->data['json'] = array('success' => 'booked');
                }
            }
        } else {
            $this->data['json'] = array('error' => validation_errors());
        }
        $this->layout(false, 'json');
    }

    function bulk_ship()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'courier_id',
                'label' => 'Courier ID',
                'rules' => 'trim|required|min_length[1]|max_length[6]|numeric'
            ),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $order_ids = $this->input->post('order_ids');
            $courier_id = $this->input->post('courier_id');

            if (!empty($order_ids)) {
                foreach ($order_ids as $order_id) {
                    if (!$this->orders_lib->processOrderShipment($order_id, $courier_id))
                        $this->session->set_flashdata('error', $this->orders_lib->get_error());
                }
            }
            $this->data['json'] = array('success' => 'booked');
        } else {
            $this->data['json'] = array('error' => validation_errors());
        }
        $this->layout(false, 'json');
    }
}