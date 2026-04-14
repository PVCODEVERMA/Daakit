<?php

use Mpdf\Tag\Em;

defined('BASEPATH') or exit('No direct script access allowed');

class Shipping extends User_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('shipping_lib');
        //$this->load->library('cargo_shipping_lib');
        $this->userHasAccess('shipments');

        $this->order_type = 'ecom';
    }

    function all($page = 1)
    {
        $per_page = $this->input->post('perPage');
        $page = $this->input->post('page') ?? 1;

        if (empty($per_page) || !is_numeric($per_page))
            $limit = 50;
        else
            $limit = $per_page;

        //load courier lsit
        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->showingToUsers($this->order_type);

        $this->data['couriers'] = $couriers;

        $filter = $this->input->post('filter');
        $apply_filters = array();

        $apply_filters['order_type'] = $this->order_type;

        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;

        if (!empty($filter['order_ids'])) {
            $apply_filters['order_ids'] = array_map('trim', explode(',', $filter['order_ids']));
        }

        if (!empty($filter['search_query'])) {
            $apply_filters['search_query'] = $filter['search_query'];
        }

        if (!empty($filter['tags'])) {
            $apply_filters['tags'] = strtolower($filter['tags']);
        }

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

        if (!empty($filter['ship_status'])) {
            $apply_filters['ship_status'] = $filter['ship_status'];
        }

        if (!empty($filter['rto_status'])) {
            $apply_filters['rto_status'] = $filter['rto_status'];
        }

        if (!empty($filter['ship_status_in']) && is_array($filter['ship_status_in'])) {
            $apply_filters['ship_status_in'] = $filter['ship_status_in'];
        }

        if (!empty($filter['stuck']) && $filter['stuck'] = 'yes') {
            $apply_filters['stuck'] = $filter['stuck'];
        }

        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }

        if (!empty($filter['warehouse_id'])) {
            $apply_filters['warehouse_id'] = $filter['warehouse_id'];
        }

        if (!empty($filter['product_name'])) {
            $apply_filters['product_name'] = $filter['product_name'];
        }

        if (!empty($filter['channel_id'])) {
            $apply_filters['channel_id'] = $filter['channel_id'];
        }

        $total_row = $this->shipping_lib->countByUserID($this->user->account_id, $apply_filters);
        $current_url=current_url()."/".$page;
        $config = array(
            'base_url' => base_url('shipping/all'),
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

        $orders = $this->shipping_lib->getByUserID($this->user->account_id, $limit, $offset, $apply_filters);
        $status_orders = array();
        $status_order_count = $this->shipping_lib->countByUserIDStatusGrouped($this->user->account_id, $apply_filters);
        if (!empty($status_order_count))
            foreach ($status_order_count as $status_count) {
                $status_orders[strtolower($status_count->ship_status)] = $status_count->total_count;
            }

        $this->load->library('warehouse_lib');
        $warehouses = $this->warehouse_lib->getUserAllWarehouse($this->user->account_id, true);

        $this->load->library('channels_lib');
        $channels = $this->channels_lib->getChannelsByUserID($this->user->account_id);
        $this->data['channels'] = $channels;

        $this->load->library('user_lib');
        $user = $this->user_lib->getByID($this->user->user_id);
        $admin = $this->user_lib->getByID($this->user->account_id);

        $this->data['user'] = $user;
        $this->data['admin'] = $admin;

        $this->data['warehouses'] = $warehouses;
        $this->data['filter'] = $filter;
        $this->data['orders'] = $orders;
        $this->data['count_by_status'] = $status_orders;

        $this->layout('shipping/index');
    }

    function exportCSV()
    {
        ini_set('memory_limit', '512M');
        $this->load->library('orders_lib');

        $filter = $this->input->get('filter');
        $apply_filters = array();

        $apply_filters['order_type'] = $this->order_type;

        $apply_filters['start_date'] = strtotime("-30 days midnight");
        $apply_filters['end_date'] = strtotime("tomorrow midnight") - 1;

        if (!empty($filter['order_ids'])) {
            $apply_filters['order_ids'] = array_map('trim', explode(',', $filter['order_ids']));
        }

        if (!empty($filter['product_name'])) {
            $apply_filters['product_name'] = $filter['product_name'];
        }

        if (!empty($filter['start_date'])) {
            $apply_filters['start_date'] = strtotime(trim($filter['start_date']) . ' 00:00:00');
        }

        if (!empty($filter['end_date'])) {
            $apply_filters['end_date'] = strtotime(trim($filter['end_date']) . ' 23:59:59');
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

        if (!empty($filter['tags'])) {
            $apply_filters['tags'] = strtolower($filter['tags']);
        }

        if (!empty($filter['rto_status'])) {
            $apply_filters['rto_status'] = $filter['rto_status'];
        }

        if (!empty($filter['search_query'])) {
            $apply_filters['search_query'] = $filter['search_query'];
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

        if (!empty($filter['awb_no'])) {
            $apply_filters['awb_no'] = array_map('trim', explode(',', $filter['awb_no']));
        }

        if (!empty($filter['warehouse_id'])) {
            $apply_filters['warehouse_id'] = $filter['warehouse_id'];
        }

        if (!empty($filter['stuck']) && $filter['stuck'] = 'yes') {
            $apply_filters['stuck'] = $filter['stuck'];
        }

        if (!empty($filter['channel_id'])) {
            $apply_filters['channel_id'] = $filter['channel_id'];
        }

        $this->data['filter'] = $filter;

        $query = $this->shipping_lib->exportByUserID($this->user->account_id, 150000000, 0, $apply_filters);

        $this->load->library('export_db');
        $export = new Export_db('slave');
        $export->query($query);

        $filename = 'Shipments_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array(
            "ID", "Shipment ID", "Order Id", "Order Date", "Shipment Date",
            "Payment", "Payment Mode","QC Status" , "Courier", "AWB Number", "RTO AWB",
            "Customer Name", "Email", "Address", "Address 2", "City", "State", "Country", "Zip Code",
            "Billing Name", "Billing Company Name", "Billing Address 1", "Billing Address 2", "Billing City", "Billing State", "Billing Country", "Billing Pincode", "Billing GST Number","Weight(gm)","Length(cm)","Height(cm)","Breadth(cm)",
            "Warehouse Name", "Warehouse City", "Warehouse State", "Warehouse Pincode", "Zone",
            "Tracking Status", "Status Update Date", "EDD", "Delivery Date", "Remittance Paid", "Remittance ID", "Remittance Date",
            "Shipping Charges", "Discount", "Courier Freight Charges", "Courier COD Charges", "Total Charges", "Courier RTO Charges",
            "Total OFD Attempts", "First OFD Date", "Last OFD Date", "Latest NDR Reason",
            "Is Essential", "Opt-in Insurance", "Insurance Amount","Pickup Date","RTO Initiated Date", "RTO Delivered Date",
            "SKU(1)", "Product(1)", "Quantity(1)", "Price(1)",
            "SKU(2)", "Product(2)", "Quantity(2)", "Price(2)",
            "SKU(3)", "Product(3)", "Quantity(3)", "Price(3)",
            "SKU(4)", "Product(4)", "Quantity(4)", "Price(4)",
            "SKU(5)", "Product(5)", "Quantity(5)", "Price(5)",
            "SKU(6)", "Product(6)", "Quantity(6)", "Price(6)",
            "SKU(7)", "Product(7)", "Quantity(7)", "Price(7)",
            "SKU(8)", "Product(8)", "Quantity(8)", "Price(8)",
            "SKU(9)", "Product(9)", "Quantity(9)", "Price(9)",
            "SKU(10)", "Product(10)", "Quantity(10)", "Price(10)"
        );

        $edd_key = '';
        $this->load->library('user_lib');
        $user = $this->user_lib->getByID($this->user->user_id);
        if (empty($user->edd_status)) {
            $edd_key = array_search('EDD', $header);
            unset($header[$edd_key]);
            $header = array_values($header);
        }

        fputcsv($file, $header);
      
        while ($order = $export->next()) {
            if(strtolower( $order->order_payment_type) == 'reverse' && empty($order->qccheck)) {
                $qcstatus = "No";
            } else if(strtolower( $order->order_payment_type) == 'reverse' && !empty($order->qccheck)) {
                $qcstatus = "Yes";
            } else {
                $qcstatus = '';
            }

            $row = array(
                $order->id,
                $order->shipment_id,
                $order->order_no,
                date('Y-m-d', $order->order_date),
                date('Y-m-d', $order->shipping_created),
                $order->order_amount,
                $order->order_payment_type,
                $qcstatus,
                $order->courier_name,
                $order->awb_number,
                $order->rto_awb,
                $order->shipping_fname . ' ' . $order->shipping_lname,
                $order->shipping_email,
                $order->shipping_address,
                $order->shipping_address_2,
                $order->shipping_city,
                $order->shipping_state,
                $order->shipping_country,
                $order->shipping_zip,
                $order->billing_fname . ' ' . $order->billing_lname,
                $order->billing_company_name,
                $order->billing_address,
                $order->billing_address_2,
                $order->billing_city,
                $order->billing_state,
                $order->billing_country,
                $order->billing_zip,
                $order->billing_gst_number,
                $order->package_weight,
                $order->package_length,
                $order->package_height,
                $order->package_breadth,
                $order->warehouse_name,
                $order->warehouse_city,
                $order->warehouse_state,
                $order->warehouse_pincode,
                $order->shipping_zone,

                ($order->ship_status == 'new') ? 'Processing' : (($order->ship_status == 'rto') ? 'RTO' . (!empty($order->rto_status) ? ucwords(' ' . $order->rto_status) : '') : ucwords($order->ship_status)),
                ($order->status_updated_at > 0) ? date('Y-m-d', $order->status_updated_at) : '',
                (!empty($order->edd_time)) ? date('Y-m-d', $order->edd_time) : '',
                (!empty($order->delivered_time)) ? date('Y-m-d', $order->delivered_time) : '',
                ($order->remittance_id > 0) ? 'Yes' : 'No',
                ($order->remittance_id > 0) ? $order->remittance_id : '',
                (!empty($order->remittance_date)) ? date('Y-m-d', $order->remittance_date) : '',
                $order->shipping_charges,
                $order->discount,
                $order->courier_fees,
                $order->cod_fees,
                $order->total_fees,
                $order->rto_charges,
                $order->total_ofd_attempts,
                ($order->first_delivery_attempt_date > 0) ? date('Y-m-d', $order->first_delivery_attempt_date) : '',
                ($order->last_attempt_date > 0) ? date('Y-m-d', $order->last_attempt_date) : '',
                $order->last_ndr_reason,
                ($order->essential_order == '1') ? 'Yes' : 'No',
                empty($order->is_insurance) ? 'No' : 'Yes',
                round($order->insurance_price, 2),
                !empty($order->pickup_time) || $order->pickup_time != 0 ? date('Y-m-d', $order->pickup_time) : '',
                !empty($order->rto_mark_date) || $order->rto_mark_date != 0 ? date('Y-m-d', $order->rto_mark_date) : '',
                !empty($order->rto_delivered_date) || $order->rto_delivered_date != 0 ? date('Y-m-d', $order->rto_delivered_date) : '',
            );

            $products = $this->orders_lib->getOrderProducts($order->id);
            if (!empty($products)) {
                foreach ($products as $prod) {
                    $row[] = !empty($prod->product_sku) ? $prod->product_sku : '';
                    $row[] = !empty($prod->product_name) ? $prod->product_name : '';
                    $row[] = !empty($prod->product_qty) ? $prod->product_qty : '';
                    $row[] = !empty($prod->product_price) ? $prod->product_price : '';       
                }
            }

            if ($edd_key && empty($user->edd_status)) {
                unset($row[$edd_key]);
                $row = array_values($row);
            }

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
            //pickup request by shipment ids
            if (!$this->shipping_lib->schedulePickup($this->user->account_id, $shipping_ids))
                $this->data['json'] = array('error' => $this->shipping_lib->get_error());
            else
                $this->data['json'] = array('success' => 'done');
        }
        $this->layout(false, 'json');
    }

    function cargo_bulk_pickup()
    {
        $shipping_ids = $this->input->post('shipping_ids');

        if (empty($shipping_ids)) {
            $this->data['json'] = array('error' => 'Please select Shipments first');
        } else {
            //pickup request by shipment ids
            if (!$this->cargo_shipping_lib->schedulePickup($this->user->account_id, $shipping_ids))
                $this->data['json'] = array('error' => $this->cargo_shipping_lib->get_error());
            else
                $this->data['json'] = array('success' => 'done');
        }
        $this->layout(false, 'json');
    }

    function bulk_cancel()
    {
        $shipping_ids = $this->input->post('shipping_ids');
        if (empty($shipping_ids)) {
            $this->data['json'] = array('error' => 'Please select Shipments first');
        } else {
            foreach ($shipping_ids as $ship_id) {
                $this->shipping_lib->cancelShipment($ship_id, $this->user->account_id);
            }
            $this->data['json'] = array('success' => 'done');
        }
        $this->layout(false, 'json');
    }

    function generate_label()
    {
        $shipping_ids = $this->input->post('shipping_ids');

        $this->load->library('user_lib');
        $user = $this->user_lib->getByID($this->user->user_id);
        
        if (!empty($shipping_ids)) {
            $pdf = $this->shipping_lib->generateLabel($shipping_ids, $user->label_format);
            $this->data['json'] = array('success' => $pdf);
        } else {
            $this->data['json'] = array('error' => 'Please select Shipments first');
        }

        $this->layout(false, 'json');
    }

    function escalate()
    {
        $this->load->library('escalation_lib');

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'shipment_id',
                'label' => 'Shipment ID',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'issue',
                'label' => 'Issue Type',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'remarks',
                'label' => 'Remarks',
                'rules' => 'trim|min_length[3]|max_length[500]'
            ),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $shipment_ids = $this->input->post('shipment_id');
            $remarks = $this->input->post('remarks');
            $issue = $this->input->post('issue');

            $shipment_ids = explode(',', $shipment_ids);
            foreach ($shipment_ids as $shipment_id) {
                //submit shipment escalation
                $update = array(
                    'type' => 'shipment',
                    'sub_type' => $issue,
                    'ref_id' => $shipment_id,
                    'remarks' => $remarks,
                    'action_by' => 'seller'
                );
                if ($this->escalation_lib->create_escalation($this->user->account_id, $update)) {
                    $this->data['json'] = array('success' => 'done');
                } else {
                    $this->data['json'] = array('error' => $this->escalation_lib->get_error());
                }
            }
        } else {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
        }
        $this->layout(false, 'json');
    }

    function pickup_list_pdf($shipping_ids)
    {
        if (!$shipping_ids)
            return false;

        $picklists = $this->shipping_lib->getByShipingid($shipping_ids);

        $totalselectedorders = $this->shipping_lib->totalselectedorders($shipping_ids);
        if (!$picklists)
            return false;

        $mpdf = new \Mpdf\Mpdf([
            'debug' => true,
            'allow_output_buffering' => true,
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_top' => 10,
            'margin_right' => 10,
            'margin_bottom' => 10,
            'tempDir' => './temp',
            'autoPageBreak' => true
        ]);

        $dataArray = array(
            'picklist' => $picklists,
            'totalselectedorders' => $totalselectedorders,
        );

        $pdf_content = $this->load->view('shipping/pickup_list', $dataArray, true);

        $mpdf->WriteHTML($pdf_content);

        $file_name = date('d-M-y') . '_' . time() . rand(1111, 9999) . '.pdf';
        $directory = 'assets/labels/';

        $mpdf->Output($directory . $file_name, 'F');

        $this->load->library('s3');
        $aws_file_name = $this->s3->amazonS3Upload($file_name, $directory . $file_name, 'pick_list');

        unlink($directory . $file_name);

        return $aws_file_name;
    }

    function pickup_list()
    {
        $shipping_ids = $this->input->post('shipping_ids');

        if (!$shipping_ids)
            return false;

        if (!empty($shipping_ids)) {
            $pdf = $this->pickup_list_pdf($shipping_ids);
            $this->data['json'] = array('success' => $pdf);
        } else {
            $this->data['json'] = array('error' => 'Please select Shipments first');
        }

        $this->layout(false, 'json');
    }
}
