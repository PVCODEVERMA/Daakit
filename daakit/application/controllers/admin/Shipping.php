<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Shipping extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('admin/shipping_lib');
        $this->load->library('admin/orders_lib');
        $this->load->library('admin/warehousehub_lib');
        $this->load->library('tracking_lib');
        $this->userHasAccess('shipments');
    }

    function index()
    {
        self::list();
    }

    function all($page = 1)
    {
        // $this->session->set_flashdata('error', 'Shipment page is under maintenance.');
        // redirect('admin', true);

        $db = new \App\Model\Shipment();

        $db->setConnection('slave');

        $per_page = $this->input->post('perPage');
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 50;
        else
            $limit = $per_page;


        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->list_couriers();
        $this->data['couriers'] = $couriers;
        $parentCouriers = $this->courier_lib->parentCourier();
        $this->data['parentCourier'] = $parentCouriers;

        $filter = $this->input->post('filter');
        $apply_filters = array();

        if (!empty($filter['order_type'])) {
            $db = $db->where('order_type', $filter['order_type']);
        }

        $apply_filters['start_date'] = strtotime("today midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;

        $date_type = 'created';

        if (!empty($filter['date_type'])) {
            $date_type = $filter['date_type'];
        }

        // admin_users


        if (!empty($filter['mul_order_type_sp'])) {
            $mul_order_type_sp = $filter['mul_order_type_sp'];
        }

        if (strtolower($date_type) == 'created') {
            if (!empty($filter['start_date'])) {
                //  echo strtotime(trim($filter['start_date']) . ' 00:00:00'); 
                $db = $db->after(strtotime(trim($filter['start_date']) . ' 00:00:00'));
            } else {
                $db = $db->after($apply_filters['start_date']);

                $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);
            }

            if (!empty($filter['end_date'])) {
                //  echo "====". strtotime(trim($filter['end_date']) . ' 23:59:59');  die;
                $db = $db->before(strtotime(trim($filter['end_date']) . ' 23:59:59'));
            } else {
                $db = $db->before($apply_filters['end_date']);
                $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);
            }
        } else {

            if (!empty($filter['start_date'])) {
                $db = $db->where('delivered_time', '>=', (strtotime(trim($filter['start_date']) . ' 00:00:00')));
            } else {
                $db = $db->where('delivered_time', '>=', $apply_filters['start_date']);

                $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);
            }

            if (!empty($filter['end_date'])) {
                $db = $db->where('delivered_time', '<=', (strtotime(trim($filter['end_date']) . ' 23:59:59')));
            } else {
                $db = $db->where('delivered_time', '>=', $apply_filters['end_date']);
                $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);
            }
        }

        if (!empty($filter['seller_id'])) {
            $db = $db->where('user_id', $filter['seller_id']);
        }

        if (!empty($filter['shipment_ids'])) {
            $db = $db->whereIn('id', array_map('trim', explode(',', $filter['shipment_ids'])));
        }

        if (!empty($filter['shipment_id'])) {
            $db = $db->whereIn('id', array_map('trim', explode(',', $filter['shipment_id'])));
        }

        if (!empty($filter['courier_id'])) {
            $db = $db->where('courier_id', $filter['courier_id']);
        }

        if (!empty($filter['parent_courier_display_name'])) {
            $get_all_ids = $this->db->select('id')->from('courier')->where('display_name', $filter['parent_courier_display_name'])->get()->result();
            $ids = [];
            foreach ($get_all_ids as $idd) {
                $ids[] = $idd->id;
            }
            $db = $db->whereIn('courier_id', $ids);
        }

        if (!empty($filter['stuck']) && $filter['stuck'] = 'yes') {
            $db = $db->where('status_updated_at', '<', strtotime('-3 days midnight'))->where('status_updated_at', '!=', '');
        }

        if (!empty($filter['open_shipments']) && $filter['open_shipments'] = 'yes') {
            $db->where(function (\Illuminate\Database\Eloquent\Builder $query) {
                return $query->whereIn('ship_status', array('in transit', 'out for delivery', 'exception'))
                    ->orWhere(
                        function (\Illuminate\Database\Eloquent\Builder $query_2) {
                            return $query_2->where('ship_status', 'rto')->where('rto_status', 'in transit');
                        }
                    );
            });
        }


        if (!empty($filter['pay_method'])) {
            $db = $db->where('payment_type', $filter['pay_method']);
        }

        if (!empty($filter['awb_no'])) {
            $db = $db->whereIn('awb_number', array_map('trim', explode(',', $filter['awb_no'])));
        }

        if (!empty($filter['account_manager_in'])) {
            $db = $db->whereIn('user_id', function ($query) use ($filter) {
                $query->select('id')
                    ->from('users')
                    ->whereIn('account_manager_id', $filter['account_manager_in']);
            });
        }

        if (!empty($filter['sale_manager_id'])) {
            $db = $db->whereIn('user_id', function ($query) use ($filter) {
                $query->select('id')
                    ->from('users');
                if ($filter['mul_order_type_sp'] == 'international') {
                    $query->whereIn('international_sale_manager_id', $filter['sale_manager_id']);
                }else if ($filter['mul_order_type_sp'] == 'b2b') {
                    $query->whereIn('b2b_sale_manager_id', $filter['sale_manager_id']);
                }else {
                    $query->whereIn('sale_manager_id', $filter['sale_manager_id']);
                }
            });
        }

        $status_query = clone $db;

        if (!empty($filter['ship_status'])) {
            $db = $db->where('ship_status', $filter['ship_status']);
        }

        if (!empty($filter['ship_status_in']) && is_array($filter['ship_status_in'])) {
            $db = $db->whereIn('ship_status', $filter['ship_status_in']);
        }

        if (!empty($filter['ship_status_not_in']) && is_array($filter['ship_status_not_in'])) {
            $db = $db->whereNotIn('ship_status', $filter['ship_status_not_in']);
        }

        if (!empty($filter['rto_status'])) {
            $db = $db->where('rto_status', $filter['rto_status']);
        }

        $count_query = clone $db;
        //echo "yes===";  pr($count_query); die;
        $total_row = $count_query->count();
        //echo "yes===total_row=";  pr($total_row); die;
        $config = array(
            'base_url' => base_url('admin/shipping/list'),
            'total_rows' => $total_row,
            'per_page' => $limit,
            'num_links' => '3',
            'use_page_numbers' => true,
            'reuse_query_string' => true,
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

        $records = $db->with(['order', 'order.products', 'user', 'weightAppliedDetails'])->limit($limit)->skip($offset)->orderBy('id', 'desc')->get();

        $this->load->library('admin/user_lib');
        $seller_details = $this->user_lib->getUserList($this->restricted_permissions);
        $this->data['users'] = $seller_details;

        $seller_details = '';
        if (!empty($filter['seller_id']))
            $seller_details = $this->user_lib->getUserListFilter($filter['seller_id']);

        $this->data['users'] = $seller_details;

        $status_orders = array();
        $status_order_count = $status_query->groupBy('ship_status')->select('ship_status', $status_query->raw('count(id) as total_count'));

        foreach ($status_order_count->get() as $status_count) {
            $status_orders[strtolower($status_count->ship_status)] = $status_count->total_count;
        }

        $this->data['filter'] = $filter;
        $this->data['records'] = $records;
        $this->data['count_by_status'] = $status_orders;
        $admin_users = $this->user_lib->getAdminUsers();
        $this->data['admin_users'] = $admin_users;

        $this->layout('shipping/list');
    }

    function list($page = 1)
    {

        // $this->session->set_flashdata('error', 'Shipment page is under maintenance.');
        // redirect('admin', true);
        $page = $this->input->post('page') ?? 1;
        $per_page = $this->input->post('perPage');
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 50;
        else
            $limit = $per_page;
        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->showingToUsers();
        $this->data['couriers'] = $couriers;
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

        if (!empty($filter['shipment_id'])) {
            $apply_filters['shipment_id'] = array_map('trim', explode(',', $filter['shipment_id']));
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

        if (!empty($filter['date_type'])) {
            $apply_filters['date_type_field'] = trim($filter['date_type']);
        } else {
            $apply_filters['date_type_field'] = 'created';
        }

        if (!empty($filter['pay_method'])) {
            $apply_filters['pay_method'] = $filter['pay_method'];
        }

        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }

        if (!empty($filter['ship_status'])) {
            $apply_filters['ship_status'] = $filter['ship_status'];
        }

        if (!empty($filter['ship_status_in']) && is_array($filter['ship_status_in'])) {
            $apply_filters['ship_status_in'] = $filter['ship_status_in'];
        }

        if (!empty($filter['ship_status_not_in']) && is_array($filter['ship_status_not_in'])) {
            $apply_filters['ship_status_not_in'] = $filter['ship_status_not_in'];
        }

        if (!empty($filter['rto_status'])) {
            $apply_filters['rto_status'] = $filter['rto_status'];
        }

        if (!empty($filter['stuck']) && $filter['stuck'] = 'yes') {
            $apply_filters['stuck'] = $filter['stuck'];
        }

        if (!empty($filter['open_shipments']) && $filter['open_shipments'] = 'yes') {
            $apply_filters['open_shipments'] = $filter['open_shipments'];
        }

        if (!empty($filter['weight_uploaded'])) {
            $apply_filters['weight_uploaded'] = $filter['weight_uploaded'];
        }

        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }

        if (!empty($filter['courier_billed'])) {
            $apply_filters['courier_billed'] = $filter['courier_billed'];
        }

        if ($this->restricted_permissions) {
            $apply_filters['account_manager_id'] = $this->user->user_id;
        }
        if (!empty($filter['accout_manager_in'])) {
            $apply_filters['accout_manager_in'] = $filter['accout_manager_in'];
        }

        if (!empty($filter['sale_manager_id'])) {
            $apply_filters['sale_manager_id'] = $filter['sale_manager_id'];
        }
        if(!empty($this->input->post('filter')) && count($this->input->post('filter'))=='1' && array_key_exists('awb_no',$this->input->post('filter')))
        {
            unset($apply_filters['start_date']);
            unset($apply_filters['end_date']);
        }
        $total_row = $this->shipping_lib->countByUserID($apply_filters);
        $current_url=current_url()."/".$page;
        $config = array(
            'base_url' => base_url('admin/shipping/all'),
            'total_rows' => $total_row,
            'per_page' => $limit,
            'num_links' => '3',
            'use_page_numbers' => true,
            'reuse_query_string' => true,
            'cur_page' => $page,
            'cur_tag_open' => '<li class="paginate_button page-item active"><a class="page-link" href="' . $current_url . '">',
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
        $orders = $this->shipping_lib->getByUserID($limit, $offset, $apply_filters);

        $this->load->library('admin/user_lib');
        $seller_details = $this->user_lib->getUserList($this->restricted_permissions);
        $this->data['users'] = $seller_details;

        $seller_details = '';
        if (!empty($filter['seller_id']))
            $seller_details = $this->user_lib->getUserListFilter($filter['seller_id']);

        $this->data['users'] = $seller_details;

        $status_orders = array();
        $status_order_count = $this->shipping_lib->countByUserIDStatusGrouped($apply_filters);
        if (!empty($status_order_count))
            foreach ($status_order_count as $status_count) {
                $status_orders[strtolower($status_count->ship_status)] = $status_count->total_count;
            }

        $this->data['filter'] = $filter;
        $this->data['orders'] = $orders;
        $this->data['count_by_status'] = $status_orders;
        $admin_users = $this->user_lib->getAdminUsers();
        $this->data['admin_users'] = $admin_users;
        $this->layout('shipping/index');
    }

    function view($id = false)
    {
        if (!$id)
            redirect('admin/orders/all', true);

        $order = $this->orders_lib->getByID($id);
        if (empty($order)) {
            $this->session->set_flashdata('error', 'Invalid Request');
            redirect('admin/orders/all', true);
        }

        // upload Customer bill
        $config = array(

            array(
                'field' => 'ship_bill',
                'label' => 'Shipment Bill',
                'rules' => 'trim|callback_file_check'
            ),
            array(
                'field' => 'ship_id',
                'label' => 'Shipping Id',
                'rules' => 'trim|required'
            )
        );
        $this->load->library('form_validation');
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $this->load->library('s3');
            $directory = 'assets/tmp/';
            $pdf_url = $this->uploadFile('ship_bill', 'international_ship_bill');

            $update = array(
                'pdf_url' => $pdf_url,
                'ship_id' => $this->input->post('ship_id')
            );
            $this->shipping_lib->upload_shipment_bill($update);
            $this->data['success'] = "Customer Shipment Bill Uploaded Successfully";
            redirect(base_url('admin/shipping/view/' . $id));
        } else {
            $this->data['error'] = validation_errors();
        }

        $shipping = $this->shipping_lib->getByOrderID($order->id);
        $courier = false;
        if (!empty($shipping)) {
            $this->load->library('courier_lib');
            $courier = $this->courier_lib->getByID($shipping->courier_id);
            $this->load->library('warehouse_lib');
            $warehouse = $this->warehouse_lib->getByID($shipping->warehouse_id);
            $rtowarehouse = $this->warehouse_lib->getByID($shipping->rto_warehouse_id);
        }

        $shippinghistory = $this->shipping_lib->getByOrderhistory($order->id);
        $ship_id = !empty($shippinghistory[0]->shipping_id) ? $shippinghistory[0]->shipping_id : '';
        $awb_number = !empty($shippinghistory[0]->awb_number) ? $shippinghistory[0]->awb_number : '';
        $ship_tracking = $this->shipping_lib->getshipmenttracking($ship_id);
        $products = $this->orders_lib->getOrderProducts($order->id);

        $shipment_metadata = [];
        foreach ($shippinghistory as $shipment) {
            if ($shipment->awb_number) {
                if (!empty($data = $this->tracking_lib->get_custom_tracking_metadata($shipment->awb_number)))
                    $shipment_metadata[$shipment->awb_number] = $data;
            }
        }

        $this->data['order'] = $order;
        $this->data['warehouse'] = $warehouse;
        $this->data['rtowarehouse'] = $rtowarehouse;
        $this->data['products'] = $products;
        $this->data['shiphistory'] = $shippinghistory;
        $this->data['ship_tracking'] = $ship_tracking;
        $this->data['shipping'] = $shipping;
        $this->data['courier'] = $courier;
        $this->data['awb_number'] = $awb_number;
        $this->data['shipment_metadata'] = $shipment_metadata;
        $this->layout('shipping/view');
    }

    function exportCsvNew()
    {
        // $this->session->set_flashdata('error', 'Shipment export is under maintenance.');
        // redirect('admin/shipping/list', true);

        ini_set('memory_limit', -1);
        ini_set('max_execution_time', '2400');
    
        $db = new \App\Model\Shipment();

        $db->setConnection('slave');

        $filter = $this->input->get('filter');
        $apply_filters = array();

        if (!empty($filter['order_type'])) {
            $db = $db->where('order_type', $filter['order_type']);
        }

        $apply_filters['start_date'] = strtotime("today midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;

        $date_type = 'created';

        if (!empty($filter['date_type'])) {
            $date_type = $filter['date_type'];
        }

        if (strtolower($date_type) == 'created') {
            if (!empty($filter['start_date'])) {
                $db = $db->after(strtotime(trim($filter['start_date']) . ' 00:00:00'));
            } else {
                $db = $db->after($apply_filters['start_date']);

                $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);
            }

            if (!empty($filter['end_date'])) {
                $db = $db->before(strtotime(trim($filter['end_date']) . ' 23:59:59'));
            } else {
                $db = $db->before($apply_filters['end_date']);
                $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);
            }
        } else {
            if (!empty($filter['start_date'])) {
                $db = $db->where('delivered_time', '>=', (strtotime(trim($filter['start_date']) . ' 00:00:00')));
            } else {
                $db = $db->where('delivered_time', '>=', $apply_filters['start_date']);

                $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);
            }

            if (!empty($filter['end_date'])) {
                $db = $db->where('delivered_time', '<=', (strtotime(trim($filter['end_date']) . ' 23:59:59')));
            } else {
                $db = $db->where('delivered_time', '>=', $apply_filters['end_date']);
                $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);
            }
        }

        if (!empty($filter['seller_id'])) {
            $db = $db->where('user_id', $filter['seller_id']);
        }

        if (!empty($filter['shipment_id'])) {
            $db = $db->whereIn('id', array_map('trim', explode(',', $filter['shipment_id'])));
        }

        if (!empty($filter['courier_id'])) {
            $db = $db->where('courier_id', $filter['courier_id']);
        }

        if (!empty($filter['parent_courier_display_name'])) {
            $get_all_ids = $this->db->select('id')->from('courier')->where('display_name', $filter['parent_courier_display_name'])->get()->result();
            $ids = [];
            foreach ($get_all_ids as $idd) {
                $ids[] = $idd->id;
            }
            $db = $db->whereIn('courier_id', $ids);
        }

        if (!empty($filter['stuck']) && $filter['stuck'] = 'yes') {
            $db = $db->where('status_updated_at', '<', strtotime('-3 days midnight'))->where('status_updated_at', '!=', '');
        }

        if (!empty($filter['open_shipments']) && $filter['open_shipments'] = 'yes') {
            $db->where(function (\Illuminate\Database\Eloquent\Builder $query) {
                return $query->whereIn('ship_status', array('in transit', 'out for delivery', 'exception'))
                    ->orWhere(
                        function (\Illuminate\Database\Eloquent\Builder $query_2) {
                            return $query_2->where('ship_status', 'rto')->where('rto_status', 'in transit');
                        }
                    );
            });
        }

        if (!empty($filter['pay_method'])) {
            $db = $db->where('payment_type', $filter['pay_method']);
        }


        if (!empty($filter['account_manager_in'])) {
            $db = $db->whereIn('user_id', function ($query) use ($filter) {
                $query->select('id')
                    ->from('users')
                    ->whereIn('account_manager_id', $filter['account_manager_in']);
            });
        }

        if (!empty($filter['sale_manager_id'])) {
            $db = $db->whereIn('user_id', function ($query) use ($filter) {
                $query->select('id')
                    ->from('users');
                if ($filter['mul_order_type_sp'] == 'international') {
                    $query->whereIn('international_sale_manager_id', $filter['sale_manager_id']);
                } else if ($filter['mul_order_type_sp'] == 'b2b') {
                    $query->whereIn('b2b_sale_manager_id', $filter['sale_manager_id']);
                } else {
                    $query->whereIn('sale_manager_id', $filter['sale_manager_id']);
                }
            });
        }

        if (!empty($filter['awb_no'])) {
            $db = $db->whereIn('awb_number', array_map('trim', explode(',', $filter['awb_no'])));
        }

        if (!empty($filter['ship_status'])) {
            $db = $db->where('ship_status', $filter['ship_status']);
        }

        if (!empty($filter['ship_status_in']) && is_array($filter['ship_status_in'])) {
            $db = $db->whereIn('ship_status', $filter['ship_status_in']);
        }

        if (!empty($filter['ship_status_not_in']) && is_array($filter['ship_status_not_in'])) {
            $db = $db->whereNotIn('ship_status', $filter['ship_status_not_in']);
        }

        if (!empty($filter['rto_status'])) {
            $db = $db->where('rto_status', $filter['rto_status']);
        }

        $filename = 'Shipments_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array(
            "delta Order ID",
            "Master Account Id",
            "Order Type",
            "delta Shipment ID",
            "delta Seller ID",
            "Order Number",
            "Order Date",
            "Shipment Date",
            "Courier",
            "Parent Courier",
            "Actual Courier",
            "AWB Number",
            "Shipment Status",
            "RTO AWB Number",
            "Seller Company",
            "Seller Name",
            "Account Manager Name",
            "Sales Person Name",
            "B2B Sales Person Name",
            "International Sales Person Name",
            "Training Manager Name",
            "Product Info",
            "Product SKU",
            "PID",
            "Product Quantity",
            "Order Amount",
            "Payment Mode",
            "QC Status",
            "Latitude",
            "Longitude",
            "Customer Name",
            "Phone Number",
            "Address",
            "Address 2",
            "City",
            "State",
            "Country",
            "Zip Code",
            "Warehouse Name",
            "Warehouse Phone",
            "Warehouse Address",
            "Warehouse City",
            "Warehouse State",
            "Warehouse Pin Code",
            "RTO Warehouse Name",
            "RTO Warehouse Phone",
            "RTO Warehouse Address",
            "RTO Warehouse City",
            "RTO Warehouse State",
            "RTO Warehouse Pin Code",
            "Zone",

            //tracking info
            "Status Updated at",
            "Pickup Request Date",
            "Pickup Date",
            "EDD",
            "Ageing From Pickup Date",
            "Reached At Destination Date",
            "No. Of Delivery Attempts",
            "First Delivery Attempt Date",
            "Last Delivery Attempt Date",
            "Expected Date of Delivery",
            "Delivery Date",
            "RTO Initiated Date",
            "RTO Delivered Date",
            "Latest NDR Reason",
            "OTP Refusal",
            "OTP Based Delivery",
            "OTP verified cancellation",
            "IVR verified cancellation",

            //remittance info
            "Courier COD Receipt ID",
            "COD Amount Received from Courier",
            "Remittance Paid",
            "Remittance ID",

            //weight info
            "Seller Dead Weight",
            "Seller LxBxH",
            "OLD Courier Charged Weight",
            "Booking Billed Weight",
            "Courier Charged Weight",
            "Seller Charged Weight",
            "Courier API Weight",
            "Weight Uploaded on",
            "Weight Applied on",
            "Pending Weight Charges(If not applied)",
            "Weight Dispute Raised",

            //billing info
            "Freight Charges",
            "COD Charges",
            "RTO Charges",
            "COD Refunds for RTO",
            "Extra Weight Charges",
            "RTO Extra Weight Charges",
            "Opt-in Insurance",
            "Insurance Amount",
            "Batch ID",
            "Enrol ID",
            "Freeze weight",
            "Freeze LxBxH",
        );


        $header = array_flip($header);
        if (!in_array('shipment_seller_detail', $this->data['user_details']->permissions)) {
            unset($header['Seller Company']); 
            unset($header['Seller Name']);
        }

        if (!in_array('shipment_product_detail', $this->data['user_details']->permissions)) {
            unset($header['Product Info']);
            unset($header['Product SKU']);
        }

        if (!in_array('shipment_customer_detail', $this->data['user_details']->permissions)) {
            unset($header['Latitude']);
            unset($header['Longitude']);
            unset($header['Customer Name']);
            unset($header['Phone Number']);
            unset($header['Address']);
            unset($header['Address 2']);
        }

        if (!in_array('shipment_warehouse_detail', $this->data['user_details']->permissions)) {
           
            unset($header['Warehouse Name']);
            unset($header['Warehouse Phone']);
            unset($header['Warehouse Address']);
            unset($header['RTO Warehouse Name']);
            unset($header['RTO Warehouse Phone']);
            unset($header['RTO Warehouse Address']);

        }

        if (!in_array('shipment_weight_detail', $this->data['user_details']->permissions)) {
            unset($header['Freeze weight']);
            unset($header['Freeze LxBxH']);
            unset($header['Courier API Weight']);
        }
        $header = array_flip($header);

       fputcsv($file, $header);
        
        $this->load->library('admin/user_lib');
        $manager_details = $this->user_lib->allgetExportShipmentUserList();
        $this->users = array();
        foreach($manager_details as $manager) {
            $this->users[$manager->id] = $manager;
        }

        $db->with(['order', 'warehouse', 'rto_warehouse', 'courier', 'tracking',   'weight', 'order.products', 'user', 'insurance', 'weightAppliedDetails'])->orderBy('id', 'desc')->chunk(4000, function ($records) use($file)  {
            $data = array();
            foreach ($records as $record) {
                $courier_weight = $this->shipping_lib->getCourierWeight($record->id);
                $actual_courier_name="";
                if(!empty($record->actual_courier_id)){
                    $actual_courier = $this->db->select('name')->from('courier')->where('id', $record->actual_courier_id)->get()->result();
                    $actual_courier_name=$actual_courier[0]->name;
                }
                if ($record->courier->order_type == 'international') {
                    $otype = "International";
                } else if ($record->courier->order_type == 'cargo') {
                    $otype = "B2B";
                } else if ($record->courier->order_type == 'hyperlocal') {
                    $otype = 'Hyperlocal';
                } else {
                    $otype = 'B2C';
                }

                if (strtolower($record->order->order_payment_type) == 'reverse' && empty($record->order->qccheck)) {
                    $qcstatus = "No";
                } else if (strtolower($record->order->order_payment_type) == 'reverse' && !empty($record->order->qccheck)) {
                    $qcstatus = "Yes";
                } else {
                    $qcstatus = '';
                }

                $freeze_length = isset($record->weightAppliedDetails->length) ? $record->weightAppliedDetails->length : "";
                $freeze_breadth = isset($record->weightAppliedDetails->breadth) ? $record->weightAppliedDetails->breadth : "";
                $freeze_height = isset($record->weightAppliedDetails->height) ? $record->weightAppliedDetails->height : "";
                $freeze_dimenstion = "";
                if ((!empty($freeze_length)) && (!empty($freeze_length)) && (!empty($freeze_length))) {
                    $freeze_dimenstion = $freeze_length . 'x' . $freeze_breadth . 'x' . $freeze_height;
                }

                 
                $product_id=array();
                if($freeze_dimenstion!=''){
             
                        if (!empty((array)$record->order->products->toArray())){
                            foreach ((array)$record->order->products->toArray() as $prod) {
    
                                $user_id      = !empty($record->user_id) ? $record->user_id : '';
                                $product_name = !empty($prod['product_name']) ? $prod['product_name'] : '';
                                $product_sku  = !empty($prod['product_sku']) ? $prod['product_sku'] : '';
                                $product_qty  = !empty($prod['product_qty']) ? $prod['product_qty'] : '';
    
                                $code = $user_id." ".$product_sku." ".$product_name." ".$product_qty ;
                                $con_code =  iconv('utf-8','ASCII//IGNORE//TRANSLIT',$code);
                                if(!empty($code)){
                                    $code  = url_title($con_code, 'underscore', TRUE);
                                }
                                    $queryd = $this->user_lib->get_data_code($code);
                                   // echo pr($queryd,1);exit;
                                    $product_id[]=  isset($queryd)?!empty($queryd->id)?$queryd->id:"":"";
                                
                            }
                        }
                }

                $users = $this->users;
              
                $account_manager = !empty($users[$record->user->account_manager_id]) ? trim($users[$record->user->account_manager_id]->user_fname . ' ' . $users[$record->user->account_manager_id]->user_lname) : '';
                $sales_manager = !empty($users[$record->user->sale_manager_id]) ? trim($users[$record->user->sale_manager_id]->user_fname . ' ' . $users[$record->user->sale_manager_id]->user_lname) : '';
                $int_sales_manager = !empty($users[$record->user->international_sale_manager_id]) ? trim($users[$record->user->international_sale_manager_id]->user_fname . ' ' . $users[$record->user->international_sale_manager_id]->user_lname) : '';
                $b2b_sales_manager = !empty($users[$record->user->b2b_sale_manager_id]) ? trim($users[$record->user->b2b_sale_manager_id]->user_fname . ' ' . $users[$record->user->b2b_sale_manager_id]->user_lname) : '';
                $training_manager = !empty($users[$record->user->training_manager_id]) ? trim($users[$record->user->training_manager_id]->user_fname . ' ' . $users[$record->user->training_manager_id]->user_lname) : '';
                
                $data['order_id'] =  $record->order->id;
                $data['account_master_id'] =  (!empty($record->user->account_master_id)) ? $record->user->account_master_id : '';
                $data['otype'] =   $otype;
                $data['record_id'] =  $record->id;
                $data['user_id'] =   $record->user_id;
                $data['record_order_id'] =   $record->order->order_id;
                $data['record_order_date'] =    date('Y-m-d', $record->order->order_date);
                $data['record_created'] =    date('Y-m-d', $record->created);
                $data['courier_name'] =     $record->courier->name;
                $data['courier_display_name'] =     $record->courier->display_name;
                $data['actual_courier_id'] =     $actual_courier_name;               
                $data['awb_number'] =     $record->awb_number;
                $data['ship_status'] =     ($record->ship_status == 'new') ? 'Processing' : (($record->ship_status == 'rto') ? 'RTO' . (!empty($record->rto_status) ? ucwords(' ' . $record->rto_status) : '') : ucwords($record->ship_status));
                $data['rto_awb'] =     $record->rto_awb;
               
                $data['company_name'] = ucwords($record->user->company_name);
                $data['user_seller'] =     ucwords($record->user->fullName());
                $data['account_manager'] =     $account_manager;
                $data['sales_manager'] =     $sales_manager;
                $data['b2b_sales_manager'] =     $b2b_sales_manager;
                $data['int_sales_manager'] =     $int_sales_manager;
                $data['training_manager'] =    $training_manager;
                $data['product_name'] =    str_replace(';', ',', implode(', ', array_column((array)$record->order->products->toArray(), 'product_name')));
                $data['product_sku'] =    str_replace(';', ',', implode(', ', array_column((array)$record->order->products->toArray(), 'product_sku')));
                $data['product_pid'] = implode(', ',$product_id);
                $data['product_qty'] = str_replace(';', ',', implode(', ', array_column((array) $record->order->products->toArray(), 'product_qty')));
                

                $data['order_amount'] =     $record->order->order_amount;
                $data['order_payment_type'] =     $record->order->order_payment_type;
                $data['qcstatus'] =    $qcstatus;
                $data['latitude'] =     (!empty($record->order->latitude)) ? $record->order->latitude : '';
                $data['longitude'] =     (!empty($record->order->longitude)) ? $record->order->longitude : '';
                $data['shipping_lname'] =    str_replace(';', ',', $record->order->shipping_fname . ' ' . $record->order->shipping_lname);
                $data['shipping_phone'] =    $record->order->shipping_phone;
                $data['shipping_address'] =     str_replace(';', ',', $record->order->shipping_address);
                $data['shipping_address_2'] =     str_replace(';', ',', $record->order->shipping_address_2);
                $data['shipping_city'] =     str_replace(';', ',', $record->order->shipping_city);
                $data['shipping_state'] =     str_replace(';', ',', $record->order->shipping_state);
                $data['shipping_country'] =     $record->order->shipping_country;
                $data['shipping_zip'] =     $record->order->shipping_zip;
                $data['warehouse_name'] =     $record->warehouse->name;
                $data['warehouse_phone'] =     $record->warehouse->phone;
                $data['warehouse_address'] =     str_replace(';', ',', $record->warehouse->address_1 . ' ' . $record->warehouse->address_2);
                $data['warehouse_city'] =     $record->warehouse->city;
                $data['warehouse_state'] =     $record->warehouse->state;
                $data['warehouse_zip'] =     $record->warehouse->zip;
                $data['rto_warehouse_name'] =     $record->rto_warehouse->name;
                $data['rto_warehouse_phone'] =     $record->rto_warehouse->phone;
                $data['rto_warehouse_address'] =     str_replace(';', ',', $record->rto_warehouse->address_1 . ' ' . $record->rto_warehouse->address_2);
                $data['rto_warehouse_city'] =     $record->rto_warehouse->city;
                $data['rto_warehouse_state'] =     $record->rto_warehouse->state;
                $data['rto_warehouse_zip'] =     $record->rto_warehouse->zip;
                $data['rto_warehouse_zone'] =     $record->zone;
                
                $data['status_updated_at'] =     ($record->status_updated_at > 0) ? date('Y-m-d', $record->status_updated_at) : '';
                $data['pending_pickup_date'] =     ($record->pending_pickup_date > 0) ? date('Y-m-d', $record->pending_pickup_date) : '';
                $data['pickup_time'] =     ($record->tracking->pickup_time > 0) ? date("Y-m-d", $record->tracking->pickup_time) : '';
                $data['edd_time'] = ($record->edd_time > 0) ? date("Y-m-d", $record->edd_time) : '';
                $data['aging_from_pickup_time'] =     ($record->tracking->pickup_time > 0) ? floor(abs(strtotime('today midnight') - strtotime(date('Y-m-d', $record->tracking->pickup_time) . ' midnight')) / 86400) : '';
                $data['reached_at_destination_hub'] =     ($record->tracking->reached_at_destination_hub != 0) ? date("Y-m-d", $record->tracking->reached_at_destination_hub) : '';
                $data['total_delivery_attempts'] =     !empty($record->tracking->delivery_attempt_count) ? $record->tracking->delivery_attempt_count : $record->tracking->total_ofd_attempts;
                $data['ofd_attempt_1_date'] =     ($record->tracking->ofd_attempt_1_date != 0) ? date("Y-m-d", $record->tracking->ofd_attempt_1_date) : '';
                $data['last_attempt_date'] =     ($record->tracking->last_attempt_date > 0) ? date("Y-m-d", $record->tracking->last_attempt_date) : '';
                $data['expected_delivery_date'] =     ($record->tracking->expected_delivery_date != 0) ? date("Y-m-d", $record->tracking->expected_delivery_date) : '';
                $data['delivered_time'] =    (!empty($record->delivered_time)) ? date('Y-m-d', $record->delivered_time) : '';
                $data['rto_mark_date'] =     (!empty($record->tracking->rto_mark_date)) ? date("Y-m-d", $record->tracking->rto_mark_date) : '';
                $data['rto_delivered_date'] =     ($record->tracking->rto_delivered_date != 0) ? date("Y-m-d", $record->tracking->rto_delivered_date) : '';
                $data['last_ndr_reason'] =     ($record->tracking->last_ndr_reason != '0') ? $record->tracking->last_ndr_reason : '';
                $data['otp_verified'] =     ($record->tracking->otp_verified == '1') ? 'Yes' : 'No';
                $data['otp_base_delivery'] =     ($record->tracking->otp_base_delivery == '1') ? 'Yes' : 'No';
                $data['otp_verified_cancelled'] =     ($record->tracking->otp_verified_cancelled == '1') ? 'Yes' : 'No';
                $data['ivr_verified_cancelled'] =     ($record->tracking->ivr_verified_cancelled == '1') ? 'Yes' : 'No';

                $data['receipt_id'] =     ($record->receipt_id > 0) ? $record->receipt_id : '';
                $data['receipt_amount'] =     ($record->receipt_amount > 0) ? $record->receipt_amount : '';
                $data['remittance_id'] =     ($record->remittance_id > 0) ? 'Yes' : 'No';
                $data['record_remittance_id'] =     ($record->remittance_id > 0) ? $record->remittance_id : '';

                $data['package_weight'] =     is_numeric($record->order->package_weight) ? $record->order->package_weight : '0';
                $data['package_length'] =     ($record->order->package_length || $record->order->package_breadth || $record->order->package_height) ? $record->order->package_length . 'x' . $record->order->package_breadth . 'x' . $record->order->package_height : '';
                $data['courier_billed_weight'] =    $record->courier_billed_weight;
                $data['calculated_weight'] =    is_numeric($record->calculated_weight) ? round($record->calculated_weight) : $record->calculated_weight;
                $data['courier_charge_weight'] =     $record->weight->courier_billed_weight;
                $data['charged_weight'] =    is_numeric($record->charged_weight) ? $record->charged_weight : '0';
                $data['courier_api_weight']  =   (isset($courier_weight) && $courier_weight->weight )? $courier_weight->weight : '0';
                $data['weight_upload_date'] =     ($record->weight->upload_date > 0) ? date('Y-m-d', $record->weight->upload_date) : '';
                $data['apply_weight_date'] =     ($record->weight->apply_weight_date > 0) ? date('Y-m-d', $record->weight->apply_weight_date) : '';
                $data['weight_difference_charges'] =     ($record->weight->weight_difference_charges > 0) ? round($record->weight->weight_difference_charges) : '0';
                $data['seller_action_status'] =     $record->weight->seller_action_status;

                //billing info
                $data['courier_fees'] =     ($record->fees_refunded == '1') ? '0' : round($record->courier_fees, 2);
                $data['fees_refunded'] =     ($record->fees_refunded == '1') ? '0' : round($record->cod_fees, 2);
                $data['rto_charges'] =     round($record->rto_charges, 2);
                $data['cod_reverse_amount'] =     round($record->cod_reverse_amount, 2);
                $data['extra_weight_charges'] =     round($record->extra_weight_charges, 2);
                $data['rto_extra_weight_charges'] =     round($record->rto_extra_weight_charges, 2);
                $data['is_insurance'] =     empty($record->is_insurance) ? 'No' : 'Yes';
                $data['insurance_price'] =     ($record->insurance_price == '1') ? '0' : round($record->insurance_price, 2);
                $data['insurance_batch_id'] =  $record->insurance->batch_id;
                $data['insurance_wtw_enrol_id'] =  $record->insurance->wtw_enrol_id;
                $data['record_weightAppliedDetails'] = isset($record->weightAppliedDetails->weight) ? $record->weightAppliedDetails->weight : "";
                $data['freeze_dimenstion'] = $freeze_dimenstion;

                if (!in_array('shipment_seller_detail', $this->data['user_details']->permissions)) {
                    unset($data['company_name']);
                    unset($data['user_seller']);
                }

        
                if (!in_array('shipment_product_detail', $this->data['user_details']->permissions)) {
                    unset($data['product_name']);
                    unset($data['product_sku']);
                }
        
                if (!in_array('shipment_customer_detail', $this->data['user_details']->permissions)) {
                    unset($data['latitude']);
                    unset($data['longitude']);
                    unset($data['shipping_lname']);
                    unset($data['shipping_phone']);
                    unset($data['shipping_address']);
                    unset($data['shipping_address_2']);
                }

                if (!in_array('shipment_warehouse_detail', $this->data['user_details']->permissions)) {
                    unset($data['warehouse_name']);
                    unset($data['warehouse_phone']);
                    unset($data['warehouse_address']);
                    unset($data['rto_warehouse_name']);
                    unset($data['rto_warehouse_phone']);
                    unset($data['rto_warehouse_address']);
                }
        
                if (!in_array('shipment_weight_detail', $this->data['user_details']->permissions)) {
                    unset($data['record_weightAppliedDetails']);
                    unset($data['freeze_dimenstion']);
                    unset($data['courier_api_weight']);
                }

                // pr($data);

                fputcsv($file, $data);
            }
        });

       fclose($file);
        exit;
    }

    function exportCSV()
    {
        $filter = $this->input->get('filter');
        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("today midnight");
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

        if (!empty($filter['shipment_id'])) {
            $apply_filters['shipment_id'] = array_map('trim', explode(',', $filter['shipment_id']));
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

        if (!empty($filter['date_type'])) {
            $apply_filters['date_type_field'] = trim($filter['date_type']);
        } else {
            $apply_filters['date_type_field'] = 'created';
        }

        if (!empty($filter['open_shipments']) && $filter['open_shipments'] = 'yes') {
            $apply_filters['open_shipments'] = $filter['open_shipments'];
        }

        if (!empty($filter['pay_method'])) {
            $apply_filters['pay_method'] = $filter['pay_method'];
        }

        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }

        if (!empty($filter['ship_status'])) {
            $apply_filters['ship_status'] = $filter['ship_status'];
        }

        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }

        if (!empty($filter['ship_status_in']) && is_array($filter['ship_status_in'])) {
            $apply_filters['ship_status_in'] = $filter['ship_status_in'];
        }

        if (!empty($filter['ship_status_not_in']) && is_array($filter['ship_status_not_in'])) {
            $apply_filters['ship_status_not_in'] = $filter['ship_status_not_in'];
        }

        if (!empty($filter['state_in'])) {
            $apply_filters['state_in'] = explode(',', $filter['state_in']);
        }

        if (!empty($filter['stuck']) && $filter['stuck'] = 'yes') {
            $apply_filters['stuck'] = $filter['stuck'];
        }

        if (!empty($filter['weight_uploaded'])) {
            $apply_filters['weight_uploaded'] = $filter['weight_uploaded'];
        }

        if (!empty($filter['courier_billed'])) {
            $apply_filters['courier_billed'] = $filter['courier_billed'];
        }

        if ($this->restricted_permissions) {
            $apply_filters['account_manager_id'] = $this->user->user_id;
        }

        if (!empty($filter['accout_manager_in'])) {
            $apply_filters['accout_manager_in'] = $filter['accout_manager_in'];
        }

        if (!empty($filter['sale_manager_id'])) {
            $apply_filters['sale_manager_id'] = $filter['sale_manager_id'];
        }

        $this->data['filter'] = $filter;
        $query = $this->shipping_lib->exportShipments(150000000, 0, $apply_filters);
        $this->load->library('export_db');

        $export = new Export_db('slave');
        $export->query($query);

        $filename = 'Shipments_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array(
            "Order ID",
            "Shipment ID",
            "Seller ID",
            "Order Number",
            "Order Date",
            "Shipment Date",
            "Courier",
            "Parent Courier",
            "Aggregator",
            "AWB Number",
            "Shipment Status",
            "Seller Company",
            "Seller Name",
            "Account Manager Name",
            "Product Info",
            "Product SKU",
            "Product Quantity",
            "Order Amount",
            "Payment Mode",
            "Customer Name",
            "Phone Number",
            "Email",
            "Address",
            "Address 2",
            "City",
            "State",
            "Zip Code",
            "Warehouse Name",
            "Warehouse Phone",
            "Warehouse Address",
            "Warehouse City",
            "Warehouse State",
            "Warehouse Pin Code",
            "Zone",

            //tracking info
            "Status Updated at",
            "Pickup Request Date",
            "Pickup Date",
            // "EDD",
            "Ageing From Pickup Date",
            "Reached At Destination Date",
            "No. Of Delivery Attempts",
            "First Delivery Attempt Date",
            "Last Delivery Attempt Date",
            "Expected Date of Delivery",
            "Delivery Date",
            "RTO Initiated Date",
            "RTO Delivered Date",
            "Latest NDR Reason",

            //remittance info
            "Courier COD Receipt ID",
            "COD Amount Received from Courier",
            "Remittance Paid",
            "Remittance ID",

            //weight info
            "Seller Dead Weight",
            "Seller LxBxH",
            "Booking Billed Weight",
            "Courier Charged Weight",
            "OLD Courier Charged Weight",
            "Seller Charged Weight",
            "Weight Uploaded on",
            "Weight Applied on",
            "Weight Reco Extra Charges",
            "Applied To Wallet",
            "Weight Reco Status",

            //billing info
            "Freight Charges",
            "COD Charges",
            "RTO Charges",
            "COD Refunds for RTO",
            "Extra Weight Charges",
            "RTO Extra Weight Charges",

            "Is Essential",
            "OTP Refusal",
            "OTP Based Delivery",
        );
        fputcsv($file, $header);
        while ($order = $export->next()) {
            $row = array(
                $order->id,
                $order->shipping_id,
                $order->userid,
                $order->order_no,
                date('Y-m-d', $order->order_date),
                date('Y-m-d', $order->shipping_created),
                $order->courier_name,
                $order->courier_display_name,
                ($order->aggregator_courier_id) ? 'Fship' : 'Courier',
                $order->awb_number,
                ($order->ship_status == 'new') ? 'Processing' : (($order->ship_status == 'rto') ? 'RTO' . (!empty($order->rto_status) ? ucwords(' ' . $order->rto_status) : '') : ucwords($order->ship_status)),
                ucwords($order->company_name),
                ucwords($order->user_fname . ' ' . $order->user_lname),
                ucwords($order->manager_fname . ' ' . $order->manager_lname),
                str_replace(';', ',', $order->products),
                str_replace(';', ',', $order->products_sku),
                $order->products_qty,

                $order->order_amount,
                $order->order_payment_type,

                str_replace(';', ',', $order->shipping_fname . ' ' . $order->shipping_lname),
                $order->shipping_phone,
                $order->shipping_email,
                str_replace(';', ',', $order->shipping_address),
                str_replace(';', ',', $order->shipping_address_2),
                str_replace(';', ',', $order->shipping_city),
                str_replace(';', ',', $order->shipping_state),
                $order->shipping_zip,

                $order->whname,
                $order->whphone,
                str_replace(';', ',', $order->address_1 . ' ' . $order->address_2),
                $order->whcity,
                $order->whstate,
                $order->whzip,
                ucwords($order->zone ?? ''),
                ($order->status_updated_at > 0) ? date('Y-m-d', $order->status_updated_at) : '',
                ($order->pending_pickup_date > 0) ? date('Y-m-d', $order->pending_pickup_date) : '',
                ($order->pickup_date > 0) ? date("Y-m-d", $order->pickup_date) : '',
                // ($order->edd_time > 0) ? date("Y-m-d", $order->edd_time) : '',
                ($order->pickup_date > 0) ? floor(abs(strtotime('today midnight') - strtotime(date('Y-m-d', $order->pickup_date) . ' midnight')) / 86400) : '',
                ($order->reached_at_destination_date != 0) ? date("Y-m-d", $order->reached_at_destination_date) : '',
                !empty($order->delivery_attempt_count) ? $order->delivery_attempt_count : $order->total_ofd_attempts,
                ($order->first_delivery_attempt_date != 0) ? date("Y-m-d", $order->first_delivery_attempt_date) : '',
                ($order->last_attempt_date > 0) ? date("Y-m-d", $order->last_attempt_date) : '',
                ($order->edd != 0) ? date("Y-m-d", $order->edd) : '',
                (!empty($order->delivered_time)) ? date('Y-m-d', $order->delivered_time) : '',
                ($order->rto_initiated_date != 0) ? date("Y-m-d", $order->rto_initiated_date) : '',
                ($order->rto_delivered_date != 0) ? date("Y-m-d", $order->rto_delivered_date) : '',
                ($order->last_ndr_reason != '0') ? $order->last_ndr_reason : '',

                ($order->receipt_id > 0) ? $order->receipt_id : '',
                ($order->receipt_amount > 0) ? $order->receipt_amount : '',
                ($order->remittance_id > 0) ? 'Yes' : 'No',
                ($order->remittance_id > 0) ? $order->remittance_id : '',

                is_numeric($order->package_weight) ? $order->package_weight : '0',
                $order->package_length . 'x' . $order->package_breadth . 'x' . $order->package_height,
                round((float) $order->calculated_weight??0),
                $order->courier_billed_weight,
                $order->old_courier_billed_weight,
                is_numeric($order->charged_weight) ? $order->charged_weight : '0',
                ($order->weight_upload_date > 0) ? date('Y-m-d', $order->weight_upload_date) : '',
                ($order->weight_applied_date > 0) ? date('Y-m-d', $order->weight_applied_date) : '',
                $order->weight_difference_charges,
                ($order->applied_to_wallet == 1) ? 'Yes' : 'No',
                $order->seller_action_status,

                //billing info
                ($order->fees_refunded == '1') ? '0' : round($order->courier_fees, 2),
                ($order->fees_refunded == '1') ? '0' : round($order->cod_fees, 2),
                round($order->rto_charges, 2),
                round($order->cod_reverse_amount, 2),
                round($order->extra_weight_charges, 2),
                round($order->rto_extra_weight_charges, 2),

                ($order->essential_order == '1') ? 'Yes' : 'No',
                ($order->otp_verified == '1') ? 'Yes' : 'No',
                ($order->otp_base_delivery == '1') ? 'Yes' : 'No'
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    function bulk_pickup()
    {
        $shipping_ids = $this->input->post('shipping_ids');
        if (empty($shipping_ids)) {
            $this->data['json'] = array('error' => 'Please select Shipments first');
        } else {
            $this->shipping_lib->schedulePickup($this->user->user_id, $shipping_ids);

            if (!empty($shipping_ids))
                $this->shipping_lib->markPickupRequested($shipping_ids);

            $this->data['json'] = array('success' => 'done');
        }
        $this->layout(false, 'json');
    }

    function generate_label()
    {
        $shipping_ids = $this->input->post('shipping_ids');

        if (!empty($shipping_ids)) {
            $pdf = $this->shipping_lib->generateLabel($shipping_ids);
            $this->data['json'] = array('success' => $pdf);
        } else {
            $this->data['json'] = array('error' => 'Please select Shipments first');
        }

        $this->layout(false, 'json');
    }

    function tracking($awb = false)
    {
        if (!$awb)
            return false;

        $shipment = $this->shipping_lib->getTrackingData($awb);
        if (empty($shipment->tracking))
            $this->data['error'] = 'Tracking Info Not Available';
        $this->data['shipment'] = $shipment;

        $this->layout('shipping/tracking');
    }

    function bulk_cancel_process()
    {
        $shipping_ids = $this->input->post('shipping_ids');
        if (empty($shipping_ids)) {
            $this->data['json'] = array('error' => 'Please select Shipments first');
        } else {
            foreach ($shipping_ids as $ship_id) {
                $cancelled = $this->shipping_lib->cancelShipment($ship_id);
                if ($cancelled) {
                    $orderId = $this->shipping_lib->getOrderId($ship_id);
                    $this->orders_lib->newordermark($orderId);
                }
            }
            $this->data['json'] = array('success' => 'done');
        }
        $this->layout(false, 'json');
    }

    public function shipmentviewAjax()
    {
        $id = $this->input->post('id');
        if (!$id)
            redirect('admin/shipping/all', true);

        $order = $this->orders_lib->getByID($id);
        if (empty($order)) {
            $this->session->set_flashdata('error', 'Invalid Request');
            redirect('admin/shipping/all', true);
        }
        $shipping = $this->shipping_lib->getByOrderID($order->id);
        $shippinghistory = $this->shipping_lib->getByOrderhistory($order->id);
        $courier = false;
        if (!empty($shipping)) {
            $this->load->library('courier_lib');
            $courier = $this->courier_lib->getByID($shipping->courier_id);
            $this->load->library('warehouse_lib');
            $warehouse = $this->warehouse_lib->getByID($shipping->warehouse_id);
        }
        $products = $this->orders_lib->getOrderProducts($order->id);
        $this->data['order'] = $order;
        $this->data['warehouse'] = $warehouse;
        $this->data['products'] = $products;
        $this->data['shipping'] = $shipping;
        $this->data['shippinghistory'] = $shippinghistory;
        $this->data['courier'] = $courier;
        return $this->load->view('admin/shipping/shipment_view', $this->data);
    }

    public function bulkShipmentSearch()
    {
        $this->layout('shipping/bulk_shipment_search');
    }

    function exportBulkShipmentSearchCSV()
    {
        if (empty($_FILES['importFile']['tmp_name'])) {
            $this->session->set_flashdata('error', 'Invalid Request');
            redirect('admin/shipping/bulkShipmentSearch', true);
        }

        $allowed_mime_types = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

        $mime = get_mime_by_extension($_FILES['importFile']['name']);
        $fileAr = explode('.', $_FILES['importFile']['name']);
        $ext = end($fileAr);
        if (($ext != 'csv') || !in_array($mime, $allowed_mime_types)) {
            $this->session->set_flashdata('error', 'Invalid File Format');
            redirect('admin/shipping/bulkShipmentSearch', true);
        }

        if (is_uploaded_file($_FILES['importFile']['tmp_name'])) {
            // Load CSV reader library
            $this->load->library('csvreader');
            // Parse data from CSV file
            $csvData = $this->csvreader->parse_csv($_FILES['importFile']['tmp_name']);

            if (empty($csvData)) {
                $this->session->set_flashdata('error', 'Blank CSV File');
                redirect('admin/shipping/bulkShipmentSearch', true);
            }

            if (count($csvData) > 5000) {
                $this->session->set_flashdata('error', 'Only 5000 AWBs are allowed');
                redirect('admin/shipping/bulkShipmentSearch', true);
            }

            $filter = array();
            foreach ($csvData as $key => $value) {
                if (!empty($value['AWB Number']) && !empty(trim(htmlspecialchars($value['AWB Number'])))) {
                    $filter[] = trim($value['AWB Number']);
                }
            }

            if (empty($filter)) {
                $this->session->set_flashdata('error', 'Blank AWB Number List');
                redirect('admin/shipping/bulkShipmentSearch', true);
            }

            $apply_filters = array();
            $apply_filters['awb_no'] = $filter;

            $query = $this->shipping_lib->exportShipments(150000000, 0, $apply_filters);
            $this->load->library('export_db');

            $export = new Export_db('slave');
            $export->query($query);

            $filename = 'Bulk_Shipments_' . time() . '.csv';
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$filename");
            header("Content-Type: application/csv; ");
            $file = fopen('php://output', 'w');
            $header = array(
                "delta Order ID",
                "delta Shipment ID",
                "delta Seller ID",
                "Order Number",
                "Order Date",
                "Shipment Date",
                "Courier",
                "AWB Number",
                "Shipment Status",
                "Seller Company",
                "Seller Name",
                "Account Manager Name",
                "Product Info",
                "Product SKU",
                "Product Quantity",
                "Order Amount",
                "Payment Mode",
                "Customer Name",
                "Phone Number",
                "Address",
                "Address 2",
                "City",
                "State",
                "Zip Code",
                "Warehouse Name",
                "Warehouse Phone",
                "Warehouse Address",
                "Warehouse City",
                "Warehouse State",
                "Warehouse Pin Code",
                "Zone",

                //tracking info
                "Status Updated at",
                "Pickup Request Date",
                "Pickup Date",
                "EDD",
                "Ageing From Pickup Date",
                "Reached At Destination Date",
                "No. Of Delivery Attempts",
                "First Delivery Attempt Date",
                "Last Delivery Attempt Date",
                "Expected Date of Delivery",
                "Delivery Date",
                "RTO Initiated Date",
                "RTO Delivered Date",
                "Latest NDR Reason",

                //remittance info
                "Courier COD Receipt ID",
                "COD Amount Received from Courier",
                "Remittance Paid",
                "Remittance ID",

                //weight info
                "Seller Dead Weight",
                "Seller LxBxH",
                "Booking Billed Weight",
                "Courier Charged Weight",
                "Seller Charged Weight",
                "Weight Uploaded on",
                "Weight Applied on",
                "Pending Weight Charges",
                "Weight Dispute Raised",

                //billing info
                "Freight Charges",
                "COD Charges",
                "RTO Charges",
                "COD Refunds for RTO",
                "Extra Weight Charges",
                "RTO Extra Weight Charges",
            );
            fputcsv($file, $header);
            while ($order = $export->next()) {

                $row = array(
                    $order->id,
                    $order->shipping_id,
                    $order->userid,
                    $order->order_id,
                    date('Y-m-d', $order->order_date),
                    date('Y-m-d', $order->shipping_created),
                    $order->courier_name,
                    $order->awb_number,
                    ($order->ship_status == 'new') ? 'Processing' : (($order->ship_status == 'rto') ? 'RTO' . (!empty($order->rto_status) ? ucwords(' ' . $order->rto_status) : '') : ucwords($order->ship_status)),
                    ucwords($order->company_name),
                    ucwords($order->user_fname . ' ' . $order->user_lname),
                    ucwords($order->manager_fname . ' ' . $order->manager_lname),
                    str_replace(';', ',', $order->products),
                    str_replace(';', ',', $order->products_sku),
                    $order->products_qty,

                    $order->order_amount,
                    $order->order_payment_type,

                    str_replace(';', ',', $order->shipping_fname . ' ' . $order->shipping_lname),
                    $order->shipping_phone,
                    str_replace(';', ',', $order->shipping_address),
                    str_replace(';', ',', $order->shipping_address_2),
                    str_replace(';', ',', $order->shipping_city),
                    str_replace(';', ',', $order->shipping_state),
                    $order->shipping_zip,

                    $order->whname,
                    $order->whphone,
                    str_replace(';', ',', $order->address_1 . ' ' . $order->address_2),
                    $order->whcity,
                    $order->whstate,
                    $order->whzip,
                    ucwords($order->zone),

                    ($order->status_updated_at > 0) ? date('Y-m-d', $order->status_updated_at) : '',
                    ($order->pending_pickup_date > 0) ? date('Y-m-d', $order->pending_pickup_date) : '',
                    ($order->pickup_date > 0) ? date("Y-m-d", $order->pickup_date) : '',
                    ($order->edd_time > 0) ? date("Y-m-d", $order->edd_time) : '',
                    ($order->pickup_date > 0) ? floor(abs(strtotime('today midnight') - strtotime(date('Y-m-d', $order->pickup_date) . ' midnight')) / 86400) : '',
                    ($order->reached_at_destination_date != 0) ? date("Y-m-d", $order->reached_at_destination_date) : '',
                    !empty($order->delivery_attempt_count) ? $order->delivery_attempt_count : $order->total_ofd_attempts,
                    ($order->first_delivery_attempt_date != 0) ? date("Y-m-d", $order->first_delivery_attempt_date) : '',
                    ($order->last_attempt_date > 0) ? date("Y-m-d", $order->last_attempt_date) : '',
                    ($order->edd != 0) ? date("Y-m-d", $order->edd) : '',
                    (!empty($order->delivered_time)) ? date('Y-m-d', $order->delivered_time) : '',
                    ($order->rto_initiated_date != 0) ? date("Y-m-d", $order->rto_initiated_date) : '',
                    ($order->rto_delivered_date != 0) ? date("Y-m-d", $order->rto_delivered_date) : '',
                    ($order->last_ndr_reason != '0') ? $order->last_ndr_reason : '',

                    ($order->receipt_id > 0) ? $order->receipt_id : '',
                    ($order->receipt_amount > 0) ? $order->receipt_amount : '',
                    ($order->remittance_id > 0) ? 'Yes' : 'No',
                    ($order->remittance_id > 0) ? $order->remittance_id : '',

                    is_numeric($order->package_weight) ? $order->package_weight : '0',
                    $order->package_length . 'x' . $order->package_breadth . 'x' . $order->package_height,
                    is_numeric($order->calculated_weight) ? round($order->calculated_weight) : $order->calculated_weight,
                    $order->courier_billed_weight,
                    is_numeric($order->charged_weight) ? $order->charged_weight : '0',
                    ($order->weight_upload_date > 0) ? date('Y-m-d', $order->weight_upload_date) : '',
                    ($order->weight_applied_date > 0) ? date('Y-m-d', $order->weight_applied_date) : '',
                    ($order->pending_weight_charges > 0) ? round($order->pending_weight_charges) : '0',
                    ($order->weight_dispute_raised == '1') ? 'Yes' : 'No',

                    //billing info
                    ($order->fees_refunded == '1') ? '0' : round($order->courier_fees, 2),
                    ($order->fees_refunded == '1') ? '0' : round($order->cod_fees, 2),
                    round($order->rto_charges, 2),
                    round($order->cod_reverse_amount, 2),
                    round($order->extra_weight_charges, 2),
                    round($order->rto_extra_weight_charges, 2),
                );
                fputcsv($file, $row);
            }
            fclose($file);
            exit;
        } else {
            $this->session->set_flashdata('error', 'Invalid Request');
            redirect('admin/shipping/bulkShipmentSearch', true);
        }
    }

    public function changePaymentType()
    {
        if (!empty($_FILES['importFile']['tmp_name'])) {
            if (!$this->shipping_lib->changePaymentTypeUpload()) {
                $this->session->set_flashdata('error', $this->shipping_lib->get_error());
                redirect('admin/shipping/list', true);
            } else {
                $this->session->set_flashdata('error', 'File uploaded successfully');
                redirect('admin/shipping/list', true);
            }
        }

        $this->layout('shipping/change_payment_type');
    }

    public function update_details()
    {
        if (!empty($_POST)) {
            $action = '';
            if ($_POST['update_type'] == 'phone') {
                $action = "change phone";
            } else if ($_POST['update_type'] == 'address') {
                $action = "change address";
            } else {
                $action = $_POST['update_type'];
            }

            $checkvalidorderid = $this->shipping_lib->checkvalidorderid($_POST['order_id']);
            if (!empty($checkvalidorderid[0]->total)) {
                $getordersData = $this->shipping_lib->getdatafromorderid($_POST['order_id']);
                $ndr_data = array(
                    'awb_number' => $_POST['awb_no'],
                    'action' => $action,
                    'change_name' => '',
                    'change_address_1' => $_POST['address1'],
                    'change_address_2' => $_POST['address2'],
                    'change_phone' => $_POST['contact'],
                    'shipping_city' => $getordersData[0]->shipping_city,
                    'shipping_state' => $getordersData[0]->shipping_state,
                    'shipping_pincode' => $getordersData[0]->shipping_zip,
                    'shipping_phone' => $getordersData[0]->shipping_phone,
                );

                $shipments = $this->shipping_lib->getByAWB($_POST['awb_no']);

                $data = array();
                switch ($shipments->courier_id) {
                    case '15': //Ekart
                    case '25': //Ekart 1 KG
                    case '27': //Ekart 2 KG
                    case '28': //Ekart 5 KG
                    case '60': //Ekart 10 KG
                    case '61': //Ekart 3 KG
                        $this->load->library('shipping/ekart');
                        $ekart = new Ekart();
                        $data = $ekart->pushNDRAction($ndr_data);
                        break;

                    case '5': //bluedart
                    case '76': //bluedart ros
                        $this->load->library('shipping/bluedart');
                        $bd = new Bluedart();
                        $data = $bd->pushNDRAction($ndr_data);
                        break;

                    case '24': //bluedart voehoo
                    case '77': //bluedart ros IN
                        $this->load->library('shipping/bluedart');
                        $bd = new Bluedart(array('mode' => 'bluedart_24'));
                        $data = $bd->pushNDRAction($ndr_data);
                        break;

                    case '12': //bluedart express
                        $this->load->library('shipping/bluedart_express');
                        $bd = new Bluedart_express();
                        $data = $bd->pushNDRAction($ndr_data);
                        break;

                    default:
                        break;
                }

                if (empty($data)) {
                    $push_ndr_status = '2';
                    $message = !empty($this->error) ? $this->error : 'API Issue';
                } else {
                    $push_ndr_status = '1';
                    $message = $data['message'];
                }

                $logs = array(
                    'order_id' => $_POST['order_id'],
                    'shipping_phone' => $getordersData[0]->shipping_phone,
                    'shipping_address' => $getordersData[0]->shipping_address,
                    'shipping_address_2' => $getordersData[0]->shipping_address_2,
                );

                $save = array(
                    'order_id' => $_POST['order_id'],
                    'data_logs' => json_encode($logs),
                    'push_ndr_status' => $push_ndr_status,
                    'push_ndr_message' => $message,
                    'push_time' => time(),
                );

                if ($this->shipping_lib->create($save)) {
                    $update_data = array(
                        'shipping_phone' => $_POST['contact'],
                        'shipping_address' => $_POST['address1'],
                        'shipping_address_2' => $_POST['address2'],
                    );

                    $order_id = $_POST['order_id'];
                    $this->shipping_lib->updaterec($order_id, $update_data);
                    echo "yes";
                }
            } else {
                echo "no";
            }
        }
    }

    public function ed_shipments()
    {
        if (!empty($_FILES['importFile']['tmp_name'])) {
            if (!$this->shipping_lib->edShipments()) {
                $this->session->set_flashdata('error', $this->shipping_lib->get_error());
                redirect('admin/shipping/ed_shipments', true);
            } else {
                $this->session->set_flashdata('error', 'File uploaded successfully');
                redirect('admin/shipping/ed_shipments', true);
            }
        }

        $this->layout('shipping/ed_shipments');
    }

    public function file_check($str)
    {
        $allowed_mime_type_arr = array('application/pdf');
        $mime = get_mime_by_extension($_FILES['ship_bill']['name']);
        if (isset($_FILES['ship_bill']['name']) && $_FILES['ship_bill']['name'] != "") {
            if (in_array($mime, $allowed_mime_type_arr)) {
                return true;
            } else {
                $this->form_validation->set_message('file_check', 'Please select only PDF file.');
                return false;
            }
        } else {
            $this->form_validation->set_message('file_check', 'Please Select File.');

            return false;
        }
    }

    private function uploadFile($variable_name = null, $folder_name = null, $image_only = false)
    {
        if ($variable_name == null || $folder_name == null) {
            return '';
        }
        $returnval = '';
        $extension = strtolower(pathinfo($_FILES[$variable_name]['name'], PATHINFO_EXTENSION));

        $new_name = time() . rand(1111, 9999) . '.' . ($extension);

        if (in_array($extension, ['pdf'])) {
            $config['file_name'] = $new_name;

            $fileTempName = $_FILES[$variable_name]['tmp_name'];
            $image_name = $new_name;

            $file_name = $this->s3->amazonS3Upload($image_name, $fileTempName, $folder_name);
            if ($file_name) {
                $returnval = $file_name;
            }
        }
        return $returnval;
    }

    public function getCustomerShipmentBill($ship_id)
    {
        if (empty($ship_id)) {
            $this->data['json'] = array('error' => 'Please select Shipments first');
        } else {
            $data = $this->shipping_lib->get_shipment_bill($ship_id);
            $this->data['json'] = array('list' => $data);
        }
        $this->layout(false, 'json');
    }

    function get_pod_awb()
    {
        $awb = $this->input->post('awb');
        $courier_id = $this->input->post('courier_id');
        if (empty($awb) || empty($courier_id)) {
            $this->data['json'] = array('error' => 'Please select Shipments first');
        } else {
            $this->load->library('courier_lib');
            $courier = $this->courier_lib->getByID($courier_id);

            $response = false;
            switch (strtolower($courier->display_name)) {
                case 'smartr':
                    $this->load->library('shipping/smartr');
                    $smartr = new Smartr();
                    $response = $smartr->podAWB($awb);
                    break;
                
                default:
                    break;
            }

            $this->data['json'] = array('success' => 'done');
        }
        $this->layout(false, 'json');
    }
}
