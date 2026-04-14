<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Analytics extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('admin/analytics_lib');
    }

    function index()
    {
        if ($this->canAccess('dashboard'))
            $this->parent_dashboard();
        else
            $this->employee_dashboard();
    }

    function employee_dashboard()
    {
        $this->layout('dash/index');
    }

    function parent_dashboard()
    {
        ini_set('max_execution_time', 120);

        $filter = $this->input->post('filter');
        $apply_filters = array();
        $start_date = strtotime('today midnight');
        $end_date = strtotime('today 23:59:59');

        if (!empty($filter['start_date'])) {
            $start_date = strtotime(trim($filter['start_date']) . ' 00:00:00');
        } else {
            $filter['start_date'] = date('Y-m-d', $start_date);
        }

        if (!empty($filter['end_date'])) {
            $end_date  = strtotime(trim($filter['end_date']) . ' 23:59:59');
        } else {
            $filter['end_date'] = date('Y-m-d', $end_date);
        }

        $total_orders = $this->analytics_lib->countOrders($start_date, $end_date);
        $this->data['total_orders'] = $total_orders;

        $total_cancelled_orders = $this->analytics_lib->countCancelledOrders($start_date, $end_date);
        $this->data['total_cancelled_orders'] = $total_cancelled_orders;

        $total_shipments = $this->analytics_lib->countShipments($start_date, $end_date);
        $this->data['total_shipments'] = $total_shipments;

        $new_users = $this->analytics_lib->countUsers($start_date, $end_date);
        $this->data['total_users'] = $new_users;

        $active_users = $this->analytics_lib->countactiveUsers($start_date, $end_date);
        $this->data['active_users'] = $active_users;

        $total_payments = $this->analytics_lib->countPayments($start_date, $end_date);
        $this->data['total_payments'] = $total_payments;

        $seller_orders = $this->analytics_lib->sellerWiseOrdersCount($start_date, $end_date);
        $this->data['seller_orders'] = $seller_orders;

        $seller_shipments = $this->analytics_lib->sellerWiseShipmentCount($start_date, $end_date);
        $this->data['seller_shipments'] = $seller_shipments;

        $courier_status = $this->analytics_lib->courierWiseStatusDistribution($start_date, $end_date);
        // $this->data['courier_status'] = $courier_status;

        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->list_couriers();
        $this->data['couriers'] = $couriers;
        $courier_stats = [];

        foreach ($courier_status as $c_array) {
            $courier_id = $c_array->courier_id;
            $courier_vise_data = array_filter($couriers, function ($v, $k) use ($courier_id) {
                return $courier_id == $v->id;
            }, ARRAY_FILTER_USE_BOTH);
            $key = array_keys($courier_vise_data);


            if (!empty($key)) {
                $data = $courier_vise_data[$key[0]];
                $courier_name = str_replace(' ', '_', strtolower($data->display_name));
                $c_array->courier_name = $data->name;
                $c_array->display_name = $data->display_name;
                $courier_stats[$courier_name][] = $c_array;
            }
        }
        //$this->data['courier_stats'] = $courier_stats;
        $this->data['courier_stats_new'] = $courier_stats;
        $currentmonth_shipments = $this->analytics_lib->countcurrentmonthShipments();

        $first_day = strtotime('first day of this month midnight');
        $last_day = strtotime('last day of this month 23:59:59');
        $now = time();
        $now_diff = $now - $first_day;
        $hours_till_now = floor($now_diff / (60 * 60));

        $month_diff = $last_day - $first_day;
        $hours_till_month_end = ceil($month_diff / (60 * 60));

        $avg_shipments = round($currentmonth_shipments / $hours_till_now);
        $this->data['avg_shipments'] = $avg_shipments * 24;
        $projected_shipment =  $avg_shipments * $hours_till_month_end;
        $this->data['projected_shipment'] = $projected_shipment;

        $this->data['filter'] = $filter;
        //pr($this->data['filter'],1);
        $this->layout('analytics/index');
    }

    public function getUserAjax($status = false)
    {
        $search = $this->input->get('searchTerm');
        if (empty($search)) {
            $json = [];
        } else {
            $seller_details = $this->user_lib->getSellerList($search, $status);
            if (!empty($seller_details)) {
                foreach ($seller_details as $ul) {
                    $json[] = ['id' => $ul->id, 'text' => $ul->id . ' - ' . $ul->user_fname . ' ' . $ul->user_lname . ' (' . $ul->company_name . ')'];
                }
            } else {
                $json = [];
            }
        }
        echo json_encode($json);
        exit();
    }
}
