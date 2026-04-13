<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reports extends User_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->userHasAccess('reports');
        $this->load->library('reports_lib');
        $this->load->library('shipping_lib');
        $this->load->library('courier_lib');
        $this->load->library('products_lib');
        $this->load->library('channels_lib');
    }

    function index()
    {
        self::v();
    }

    function v($page = 'generate_report', $page_no = 1)
    {

        $inner_content = '';
        switch ($page) {
            case 'generate_report':
                $inner_content = $this->generate_report();
                break;
            case 'daily_summary':
                $inner_content = $this->daily_order_summary();
                break;
            case 'state_summary':
                $inner_content = $this->state_order_summary();
                break;
            case 'product_summary':
                $inner_content = $this->product_wise_summary();
                break;
            case 'courier_summary':
                $inner_content = $this->courierWiseSummary();
                break;
            case 'zone_summary':
                $inner_content = $this->zoneWiseSummary();
                break;
            case 'channel_summary':
                $inner_content = $this->channel_wise_summary();
                break;

            case 'operation':
                $inner_content = $this->top_dashboard_new();
                break;



            default:
                $inner_content = $this->daily_order_summary();
                break;
        }

        $this->data['inner_content'] = $inner_content;

        $this->data['view_page'] = $page;
        $this->layout('reports/view');
    }

    function top_dashboard_new($page = 'dashboard', $page_no = 1)
    {
        $this->load->library('operation_lib');
        $inner_content = '';
        switch ($page) {
            case 'dashboard':
                $inner_content = $this->top_dashboard();
                break;

            default:
                $inner_content = $this->top_dashboard();
        }

        return $this->data['inner_content'] = $inner_content;
        //echo "hello ---";pr($this->data['inner_content']); die;
        // $this->data['view_page'] = $page;
        // return $this->layout('reports/view');
        // $this->layout('operation/view');
    }

    function top_dashboard()
    {
        $this->load->library('operation_lib');
        $ndr_keys = array(
            'wrong_mobile' => 'Wrong Mobile',
            'reschedule' => 'Future Delivery',
            'wrong_address' => 'Wrong Address',
            'customer_cancelled' => 'Cancelled by Customer',
            'amount_not_ready' => 'Payment Not Ready',
            'unavailable' => 'Customer Not Available',
            'open_delivery' => 'Requested Open Delivery',
            'contact_courier' => 'Contact Courier',
            'others' => 'Others',
            'closed' => 'Premises Closed',
            'restricted' => 'Restricted Area',
            'need_details' => 'Incomplete Address',
            'oda' => 'ODA Location',
            'self_collect' => 'Self Collect',
            'wrong_pincode' => 'Wrong Pincode'
        );
        $records = $this->operation_lib->NDRByMessageGrouped($this->user->account_id);


        $this->load->library('ndr_lib');

        $ndrs = array();

        foreach ($records as $record) {
            $s = $this->ndr_lib->filterExceptionMessage($record->ndr_remarks);

            if ($s != '') {
                $ndrs[$s][] = $record;
            } else {
                $ndrs['others'][] = $record;
            }
        }

        $ndr_data = array();
        if (!empty($ndrs)) {
            foreach ($ndrs as $key => $single) {
                $total_count = array_sum(array_column($single, 'total_count'));
                $ndr_ids = implode(',', array_column($single, 'ndr_ids'));
                $ndr_data[] = array(
                    'ndr_remarks' => array_key_exists($key, $ndr_keys) ? $ndr_keys[$key] : $key,
                    'total_count' => $total_count,
                    'ndr_ids' => $ndr_ids
                );
            }
        }

        array_multisort(array_column($ndr_data, 'total_count'), SORT_DESC, $ndr_data);

        $this->data['ndr_data'] = $ndr_data;

        return $this->load->view('operation/dashboard', $this->data, true);
    }

    function zoneWiseSummary()
    {

        $filter = $this->input->get('filter');
        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;
        if (!empty($filter['start_date']))
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        else
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);
        if (!empty($filter['end_date']))
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        else
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);

        if (!empty($filter['zone'])) {
            $apply_filters['zone'] = $filter['zone'];
        }

        $zoneWise['details'] = $this->shipping_lib->getUserWiseZoneDetail($this->user->account_id, $apply_filters);
        $zoneWise['filter'] = $filter;

        // pr($zoneWise,1); 


        return $this->load->view('reports/pages/zone_wise_summary', $zoneWise, true);
    }
    function courierWiseSummary()
    {

        $per_page = $this->input->get('perPage');
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 10;
        else
            $limit = $per_page;

        $filter = $this->input->get('filter');
        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;


        if (!empty($filter['start_date']))
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        else
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);


        if (!empty($filter['end_date']))
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        else
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);

        if (!empty($filter['name']))
            $apply_filters['name'] = $filter['name'];

        if (!empty($filter['payment_type']))
            $apply_filters['payment_type'] = $filter['payment_type'];

        $courierwise['names'] = $this->courier_lib->getAllCouriersDetail($this->user->account_id);

        $courierwise['orders'] = $this->courier_lib->getAllOrderCouriersDetail($this->user->account_id, $apply_filters);

        $courierwise['couriers'] = $this->courier_lib->showingToUsers();

        $courierCompData = [];
        foreach ($courierwise['orders'] as $comp) {
            $c_id = $comp->courier_id;
            $courierData = array_filter($courierwise['couriers'], function ($v, $k) use ($c_id) {
                return $c_id == $v->id;
            }, ARRAY_FILTER_USE_BOTH);
            $key = array_keys($courierData);
            if (!empty($key)) {
                $new = $courierData[$key[0]];
                $c_name = str_replace(' ', '_', strtolower($new->display_name));
                $comp->courier_name = $new->name;
                $comp->display_name = $new->display_name;
                $courierCompData[$c_name][] = $comp;
            }
        }
        $courierwise['compdata'] = $courierCompData;
        $courierwise['filter'] = $filter;

        return  $this->load->view('reports/pages/courier_wise_summary', $courierwise, true);
    }


    function channel_wise_summary()
    {

        $filter = $this->input->get('filter');
        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;
        if (!empty($filter['start_date']))
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        else
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);
        if (!empty($filter['end_date']))
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        else
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);

        if (!empty($filter['channel_name']))
            $apply_filters['channel_name'] = $filter['channel_name'];

        $userChannel['channel'] = $this->channels_lib->getUserChannelList($this->user->account_id);
        $userChannel['details'] = $this->channels_lib->getUserChannelDetail($this->user->account_id, $apply_filters);

        $userChannel['filter'] = $filter;

        //  pr($userChannel['details'],1);

        return $this->load->view('reports/pages/channel_wise_summary', $userChannel, true);
    }
    function product_wise_summary()
    {

        $per_page = $this->input->get('perPage');
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 10;
        else
            $limit = $per_page;
        $filter = $this->input->get('filter');

        $apply_filters = array();
        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;

        if (!empty($filter['start_date']))
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        else
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);

        if (!empty($filter['end_date']))
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        else
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);

        if (!empty($filter['product_name'])) {
            $apply_filters['product_name'] = $filter['product_name'];
        }

        $userProduct['orders'] = $this->products_lib->getUserProductDetails($this->user->account_id, $apply_filters);
        $userProduct['filter'] = $filter;
        return  $this->load->view('reports/pages/product_wise_summary', $userProduct, true);
    }

    function generate_report()
    {

        $filter = $this->input->post('filter');
        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;

        if (!empty($filter['start_date']))
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        else
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);
        if (!empty($filter['end_date']))
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        else
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);
        $this->data['filter'] = $filter;

        $this->generateReportNew();
        return $this->load->view('reports/pages/generate_report', $this->data, true);
    }

    function generateReport()
    {
        ini_set('memory_limit', '512M');
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'fields[]',
                'label' => 'Fields',
                'rules' => 'trim|required'
            ),

        );
        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['error'] = validation_errors();
            return false;
        }
        $filter = $this->input->post('filter');
        $fields = $this->input->post('fields[]');

        $apply_filters = array();
        $all_fields = array(
            'channel_id' => 'Channel Name',
            'order_number' => '#Number',
            'order_date' => 'Order Date',
            'order_amount' => 'Amount',
            'order_payment_type' => 'Payment Type',
            'shipping_fname' => 'First Name',
            'shipping_lname' => 'Last Name',
            'shipping_address' => 'Address 1',
            'shipping_address_2' => 'Address 2',
            'shipping_phone' => 'Phone',
            'shipping_city' => 'City',
            'shipping_state' => 'State',
            'shipping_zip' => 'Pincode',
            'package_weight' => 'Weight',
            'package_length' => 'Length',
            'package_height' => 'Height',
            'package_breadth' => 'Breadth',
            'fulfillment_status' => 'Order Status',
            'order_tags' => 'Shopify Order Tags',
            'shipping_charges' => 'Shipping Charges',
            'discount' => 'Discount Applied',
            'courier_id' => 'Courier Name',
            'shipment_date' => 'Shipment Date',
            'awb_number' => 'AWB Number',
            'ship_status' => 'Shipment Status',
            'remittance_id' => 'Remittance ID',
            'pickup_time' => 'Pickup Time',
            'delivered_time' => 'Delivered Time',
            'charged_weight' => 'Charged Weight',
            'zone' => 'Zone',
            'status_updated_at' => 'Last Status Updated',
            'warehouse_name' => 'Warehouse Name',
            'warehouse_contact_name' => 'Contact Name',
            'warehouse_phone' => 'Contact Number',
            'warehouse_address_1' => 'Address 1',
            'warehouse_address_2' => 'Address 2',
            'warehouse_city' => 'City',
            'warehouse_state' => 'State',
            'warehouse_zip' => 'Pincode',
        );
        if (!empty($filter['start_date']))
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        else
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);

        if (!empty($filter['end_date']))
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        else
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);


        $apply_filters['user_id'] = $this->user->account_id;

        $query = $this->reports_lib->shipmentsByUserID($apply_filters);

        $this->load->library('export_db');
        $export = new Export_db('slave');
        $export->query($query);

        $filename = 'Report_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array('Shipment ID');
        foreach ($fields as $f) {
            if (array_key_exists($f, $all_fields)) {
                $header[] = $all_fields[$f];
            }
        }

        // if (in_array('attempts', $fields)) {
        //     $header[] = 'Attempts Count';
        //     $header[] = 'Last Exception Date';
        //     $header[] = 'Exception Remarks';
        // }

        if (in_array('products', $fields)) {
            $header[] = "SKU(1)";
            $header[] =  "Product(1)";
            $header[] = "Quantity(1)";
            $header[] = "Price(1)";
            $header[] = "SKU(2)";
            $header[] = "Product(2)";
            $header[] = "Quantity(2)";
            $header[] = "Price(2)";
            $header[] = "SKU(3)";
            $header[] = "Product(3)";
            $header[] = "Quantity(3)";
            $header[] = "Price(3)";
            $header[] = "SKU(4)";
            $header[] = "Product(4)";
            $header[] = "Quantity(4)";
            $header[] = "Price(4)";
            $header[] = "SKU(5)";
            $header[] = "Product(5)";
            $header[] = "Quantity(5)";
            $header[] = "Price(5)";
            $header[] = "SKU(6)";
            $header[] = "Product(6)";
            $header[] = "Quantity(6)";
            $header[] = "Price(6)";
            $header[] = "SKU(7)";
            $header[] = "Product(7)";
            $header[] = "Quantity(7)";
            $header[] = "Price(7)";
            $header[] = "SKU(8)";
            $header[] = "Product(8)";
            $header[] = "Quantity(8)";
            $header[] = "Price(8)";
            $header[] = "SKU(9)";
            $header[] = "Product(9)";
            $header[] = "Quantity(9)";
            $header[] = "Price(9)";
            $header[] = "SKU(10)";
            $header[] = "Product(10)";
            $header[] = "Quantity(10)";
            $header[] = "Price(10)";
        }

        fputcsv($file, $header);

        $all_shipments = array();
        $ndr_data = array();
        while ($shipment = $export->next()) {
            if (!array_key_exists($shipment->shipment_id, $all_shipments)) {
                $all_shipments[$shipment->shipment_id] = $shipment;
            }
            $all_shipments[$shipment->shipment_id]->products[] = (object) array(
                'name' => $shipment->product_name,
                'qty' => $shipment->product_qty,
                'price' => $shipment->product_price,
                'sku' => $shipment->product_sku,
            );
            if (!empty($shipment->remarks)) {
                $all_remarks =   explode("|||", $shipment->remarks);
                if (!empty($all_remarks)) {
                    foreach ($all_remarks as $single_remark) {
                        $single_remark = explode('<->', $single_remark);

                        $shipmentId = (!empty($single_remark[0])) ? $single_remark[0] : '';
                        $attempt = (!empty($single_remark[1])) ? $single_remark[1] : '';
                        $event_time = (!empty($single_remark[2])) ? $single_remark[2] : '';
                        $remarks = (!empty($single_remark[3])) ? $single_remark[3] : '';
                        $ndr_data[$shipmentId][$attempt] = array(
                            'time' => $event_time,
                            'remarks' => $remarks
                        );
                    }
                    array_multisort(array_column($ndr_data, 'time'), SORT_DESC, $ndr_data);
                }
            }
        }
        //pr($all_shipments);
        /* $shipment_ids = array_column($all_shipments, 'shipment_id');

        $this->load->library('ndr_lib');
        $all_ndr = $this->ndr_lib->getAllNDR($shipment_ids);

        $ndr_data = array();
        if (!empty($all_ndr)) {
            foreach ($all_ndr as $a_ndr) {
                $ndr_data[$a_ndr->shipment_id][$a_ndr->attempt] = array(
                    'time' => $a_ndr->event_time,
                    'remarks' => $a_ndr->remarks
                );
            }
        }
            
        */

        foreach ($all_shipments as $shipment) {
            $row = array();
            $row[] = $shipment->shipment_id;

            foreach ($fields as  $f) {
                if (array_key_exists($f, $all_fields)) {
                    switch ($f) {
                        case 'order_date':
                        case 'shipment_date':
                        case 'pickup_time':
                        case 'delivered_time':
                        case 'status_updated_at':
                            $row[] = ($shipment->{$f} > 0) ? date('Y-m-d', $shipment->{$f}) : '';
                            break;
                        case 'ship_status':
                            $row[] = ($shipment->ship_status == 'rto') ? $shipment->ship_status . ' ' . $shipment->rto_status : $shipment->ship_status;
                            break;
                        default:
                            $row[] = $shipment->{$f};
                    }
                }
            }
            // if (in_array('attempts', $fields)) {
            //     if (!empty($ndr_data[$shipment->shipment_id])) {
            //         $ndr_info = $ndr_data[$shipment->shipment_id];
            //         $row[] = count($ndr_info);

            //         array_multisort(array_column($ndr_info, 'time'), SORT_DESC, $ndr_info);
            //         $row[] = date('Y-m-d', $ndr_info[0]['time']);
            //         $row[] = $ndr_info[0]['remarks'];
            //     } else {
            //         $row[] = '';
            //         $row[] = '';
            //         $row[] = '';
            //     }
            // }
            if (in_array('products', $fields)) {
                for ($i = 0; $i <= 9; $i++) {
                    $row[] = array_key_exists($i, $shipment->products) ? $shipment->products[$i]->sku : '';
                    $row[] = array_key_exists($i, $shipment->products) ? $shipment->products[$i]->name : '';
                    $row[] = array_key_exists($i, $shipment->products) ? $shipment->products[$i]->qty : '';
                    $row[] = array_key_exists($i, $shipment->products) ? $shipment->products[$i]->price : '';
                }
            }
            fputcsv($file, $row);
        }
        exit;
    }
    function daily_order_summary($page = 1)
    {
        $per_page = $this->input->get('perPage');
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 10;
        else
            $limit = $per_page;

        //load courier lsit
        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->userAvailableCouriers($this->user->account_id);

        $this->data['couriers'] = $couriers;

        $filter = $this->input->get('filter');
        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;


        if (!empty($filter['start_date']))
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        else
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);


        if (!empty($filter['end_date']))
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        else
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);


        if (!empty($filter['pay_method'])) {
            $apply_filters['pay_method'] = $filter['pay_method'];
        }

        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }


        if (!empty($filter['product_name'])) {
            $apply_filters['product_name'] = $filter['product_name'];
        }





        $total_row = $this->reports_lib->count_daily_order_summary($this->user->account_id, $apply_filters);

        $config = array(
            'base_url' => base_url('reports/v/daily_summary'),
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


        $shipments = $this->reports_lib->daily_order_summary($this->user->account_id, $limit, $offset, $apply_filters);

        $this->data['filter'] = $filter;
        $this->data['shipments'] = $shipments;

        return $this->load->view('reports/pages/daily_order_summary', $this->data, true);
    }

    function state_order_summary($page = 1)
    {
        $per_page = $this->input->get('perPage');
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 10;
        else
            $limit = $per_page;

        //load courier lsit
        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->userAvailableCouriers($this->user->account_id);

        $this->data['couriers'] = $couriers;

        $filter = $this->input->get('filter');
        $apply_filters = array();

        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;


        if (!empty($filter['start_date']))
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        else
            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);


        if (!empty($filter['end_date']))
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
        else
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);

        if (!empty($filter['pay_method'])) {
            $apply_filters['pay_method'] = $filter['pay_method'];
        }

        if (!empty($filter['courier_id'])) {
            $apply_filters['courier_id'] = $filter['courier_id'];
        }


        if (!empty($filter['product_name'])) {
            $apply_filters['product_name'] = $filter['product_name'];
        }



        $summary = $this->reports_lib->pincode_wise_summary($this->user->account_id, $apply_filters);

        $this->load->config('pincodes');
        $pincode_states = $this->config->item('pincode_states');

        $states_summary = array();
        if (!empty($summary)) {
            foreach ($summary as $sum) {
                if (array_key_exists($sum->pincode_first_letter, $pincode_states)) {
                    $states_summary[$pincode_states[$sum->pincode_first_letter]][] = $sum;
                } else {
                    $states_summary['others'][] = $sum;
                }
            }

            if (!empty($states_summary)) {
                foreach ($states_summary as $state => $s_m) {
                    $states_summary[$state] = array(
                        'state' => $state,
                        'codes' => array_column($s_m, 'pincode_first_letter'),
                        'in_transit' => array_sum(array_column($s_m, 'in_transit')),
                        'delivered' => array_sum(array_column($s_m, 'delivered')),
                        'rto' => array_sum(array_column($s_m, 'rto')),
                        'exception' => array_sum(array_column($s_m, 'exception')),
                        'total' => array_sum(array_column($s_m, 'total')),
                    );
                }
            }

            array_multisort(array_column($states_summary, 'total'), SORT_DESC, $states_summary);
        }



        $this->data['filter'] = $filter;
        $this->data['shipments'] = json_decode(json_encode($states_summary));

        return $this->load->view('reports/pages/state_wise_summary', $this->data, true);
    }

    function daily_order_summary_products()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'expand_date',
                'label' => 'Date',
                'rules' => 'trim|required'
            ),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $expand_date = $this->input->post('expand_date');

            //fetch products details


            $start_date = strtotime(trim($expand_date) . ' 00:00:00');


            $end_date = strtotime(trim($expand_date) . ' 23:59:59');


            $shipments = $this->reports_lib->productWiseStatusDistribution($this->user->account_id, $start_date, $end_date);

            $this->data['shipments'] = $shipments;
            $this->data['json'] = array('success' => $this->load->view('reports/daily_order_summary_products', $this->data, true));
        } else {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
        }
        $this->layout(false, 'json');
    }

    function state_order_summary_couriers()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'expand_state',
                'label' => 'States',
                'rules' => 'trim|required'
            ),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $expand_states = $this->input->post('expand_state');
            $expand_states = explode('_', $expand_states);

            $start_date = $this->input->post('start_date');
            $end_date = $this->input->post('end_date');

            if (!empty($start_date))
                $start_date = strtotime(trim($start_date) . ' 00:00:00');
            else
                $start_date = strtotime("first day of oct 2019 midnight");

            if (!empty($end_date))
                $end_date = strtotime(trim($end_date) . ' 23:59:59');
            else
                $end_date = time();


            $shipments = $this->reports_lib->courierWiseStateStatusDistribution($this->user->account_id, $expand_states, $start_date, $end_date);


            $this->data['shipments'] = $shipments;
            $this->data['json'] = array('success' => $this->load->view('reports/state_order_summary_couriers', $this->data, true));
        } else {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
        }
        $this->layout(false, 'json');
    }

    function generateReportNew()
    {
        ini_set('max_execution_time', 600);
        $db = new \App\Model\Shipment();

        $db->setConnection('slave');
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'fields[]',
                'label' => 'Fields',
                'rules' => 'trim|required'
            ),

        );
        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['error'] = validation_errors();
            return false;
        }
        $filter = $this->input->post('filter');
        $fields = $this->input->post('fields[]');

        $apply_filters = array();
        $all_fields = array(
            //'channel_id' =>array("name"=>"Channel Name", "path"=>"order", 'field'=>'channel_id'),
            'order_number' =>       array("name" => "#Number", "path" => "order", 'field' => 'order_id'),
            'order_date' =>         array('name' => 'Order Date', 'path' => 'order', 'field' => 'order_date'),
            'order_amount' =>       array('name' => 'Order Amount', 'path' => 'order', 'field' => 'order_amount'),
            'order_payment_type' => array('name' => 'Payment Type', 'path' => 'order', 'field' => 'order_payment_type'),
            'shipping_fname' =>     array('name' => 'First Name', 'path' => 'order', 'field' => 'shipping_fname'),
            'shipping_lname' =>     array('name' => 'Last Name', 'path' => 'order', 'field' => 'shipping_lname'),
            'shipping_address' =>   array('name' => 'Address 1', 'path' => 'order', 'field' => 'shipping_address'),
            'shipping_address_2' => array('name' => 'Address 2', 'path' => 'order', 'field' => 'shipping_address_2'),
            'shipping_phone' =>     array('name' => 'Phone', 'path' => 'order', 'field' => 'shipping_phone'),
            'shipping_city' =>      array('name' => 'City', 'path' => 'order', 'field' => 'shipping_city'),
            'shipping_state' =>     array('name' => 'State', 'path' => 'order', 'field' => 'shipping_state'),
            'shipping_zip' =>       array('name' => 'Pincode', 'path' => 'order', 'field' => 'shipping_zip'),
            'package_weight' =>     array('name' => 'Weight', 'path' => 'order', 'field' => 'package_weight'),
            'package_length' =>     array('name' => 'Length', 'path' => 'order', 'field' => 'package_length'),
            'package_height' =>     array('name' => 'Height', 'path' => 'order', 'field' => 'package_height'),
            'package_breadth' =>    array('name' => 'Breadth', 'path' => 'order', 'field' => 'package_breadth'),
            'fulfillment_status' => array('name' => 'Order Status', 'path' => 'order', 'field' => 'fulfillment_status'),
            'shipping_charges' =>   array('name' => 'Shipping Charges', 'path' => 'order', 'field' => 'shipping_charges'),
            'discount' =>           array('name' => 'Discount Applied', 'path' => 'order', 'field' => 'discount'),
            'order_tags' =>         array('name' => 'Shopify Order Tags', 'path' => 'order', 'field' => 'order_tags'),

            // 'courier_id' => array('name'=>'Courier Name', 'path'=>'order','field'=>''),
            //Shipment
            'shipment_date' =>      array('name' => 'Shipment Date', 'path' => 'ship', 'field' => 'created'),
            'awb_number' =>         array('name' => 'AWB Number', 'path' => 'ship', 'field' => 'awb_number'),
            'ship_status' =>        array('name' => 'Shipment Status', 'path' => 'ship', 'field' => 'ship_status'),
            'remittance_id' =>      array('name' => 'Remittance ID', 'path' => 'ship', 'field' => 'remittance_id'),
            'pickup_time' =>        array('name' => 'Pickup Time', 'path' => 'ship', 'field' => 'pickup_time'),
            'delivered_time' =>     array('name' => 'Delivered Time', 'path' => 'ship', 'field' => 'delivered_time'),
            'charged_weight' =>     array('name' => 'Charged Weight', 'path' => 'ship', 'field' => 'charged_weight'),
            'zone' =>               array('name' => 'Zone', 'path' => 'ship', 'field' => 'zone'),
            'status_updated_at' =>  array('name' => 'Last Status Updated', 'path' => 'ship', 'field' => 'status_updated_at'),
            'warehouse_name' =>     array('name' => 'Warehouse Name', 'path' => 'warehouse', 'field' => 'name'),
            'warehouse_contact_name' => array('name' => 'Contact Name', 'path' => 'warehouse', 'field' => 'contact_name'),
            'warehouse_phone' =>    array('name' => 'Contact Number', 'path' => 'warehouse', 'field' => 'phone'),
            'warehouse_address_1' => array('name' => 'Address 1', 'path' => 'warehouse', 'field' => 'address_1'),
            'warehouse_address_2' => array('name' => 'Address 2', 'path' => 'warehouse', 'field' => 'address_2'),
            'warehouse_city' =>     array('name' => 'City', 'path' => 'warehouse', 'field' => 'city'),
            'warehouse_state' =>    array('name' => 'State', 'path' => 'warehouse', 'field' => 'state'),
            'warehouse_zip' =>      array('name' => 'Pincode', 'path' => 'warehouse', 'field' => 'zip'),
        );
        $apply_filters['start_date'] = strtotime("today midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;

        if (!empty($filter['start_date'])) {
            $db = $db->after(strtotime(trim($filter['start_date']) . ' 00:00:00'));
        } else {
            $db =  $db->after($apply_filters['start_date']);

            $filter['start_date'] = date('Y-m-d', $apply_filters['start_date']);
        }
        if (!empty($filter['end_date'])) {
            $db = $db->before(strtotime(trim($filter['end_date']) . ' 23:59:59'));
        } else {
            $db = $db->before($apply_filters['end_date']);
            $filter['end_date'] = date('Y-m-d', $apply_filters['end_date']);
        }

        $db = $db->where('user_id', $this->user->account_id);
        $apply_filters['user_id'] = $this->user->account_id;

        $filename = 'Report_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array('Shipment ID');
        foreach ($fields as $f) {
            if (array_key_exists($f, $all_fields)) {
                $header[] = $all_fields[$f]['name'];
            }
        }
        if (in_array('products', $fields)) {
            for ($x = 1; $x <= 10; $x++) {
                $header[] = "SKU($x)";
                $header[] =  "Product($x)";
                $header[] = "Quantity($x)";
                $header[] = "Price($x)";
            }
        }
        fputcsv($file, $header);
        $all_shipments = array();
        $ndr_data = array();
        $db->with(['order', 'order.products', 'warehouse'])->orderBy('id', 'desc')->chunk(2000, function ($records) use ($file, &$fields, &$all_fields) {
            foreach ($records as $shipment) {
                $order = $ship_data = $row = $product = $ship = $product_data = $warehouse = array();
                $ship = $shipment;
                $order = $shipment->order;
                $product = $shipment->order->products;
                $warehouse = $shipment->warehouse;
                $ship_data[] = $ship->id;
                foreach ($fields as  $f) {
                    if (!empty($all_fields[$f]) && $f != 'products') {
                        $field_value = ${$all_fields[$f]['path']}->{$all_fields[$f]['field']};

                        switch ($f) {
                            case 'order_date':
                            case 'shipment_date':
                            case 'pickup_time':
                            case 'delivered_time':
                            case 'status_updated_at':
                                $ship_data[] = ($field_value > 0) ? date('Y-m-d', $field_value) : '';
                                break;
                            case 'ship_status':
                                $ship_data[] = ($field_value == 'rto') ? $field_value . ' ' . $shipment->rto_status : $field_value;
                                break;
                            default:
                                $ship_data[] = $field_value;
                        }
                    }
                }
                if (in_array('products', $fields)) {
                    for ($i = 0; $i <= 9; $i++) {
                        $product_data[] = (!empty($shipment['order']['products'][$i]['product_sku'])) ? $shipment['order']['products'][$i]['product_sku'] : '';
                        $product_data[] = (!empty($shipment['order']['products'][$i]['product_name'])) ? $shipment['order']['products'][$i]['product_name'] : '';
                        $product_data[] = (!empty($shipment['order']['products'][$i]['product_qty'])) ? $shipment['order']['products'][$i]['product_qty'] : '';
                        $product_data[] = (!empty($shipment['order']['products'][$i]['product_price'])) ? $shipment['order']['products'][$i]['product_price'] : '';
                    }
                }
                $row = array_merge($ship_data, $product_data);
                fputcsv($file, $row);
            }
        });
        exit;
    }
}
