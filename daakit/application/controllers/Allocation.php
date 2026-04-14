<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Allocation extends User_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('allocation_lib');
        $this->userHasAccess('apps');
    }

    function index()
    {
        $this->v();
    }

    function v($page = 'rules', $page_no = 1)
    {
        $inner_content = '';
        switch ($page) {
            case 'rules':
                $inner_content = $this->rules();
                break;
            case 'edit_rule':
                $inner_content = $this->edit($page_no);
                break;
            case 'test_rule':
                $inner_content = $this->test_rule($page_no);
                break;

            default:
                $inner_content = $this->rules();
        }

        $this->data['inner_content'] = $inner_content;

        $this->data['view_page'] = $page;
        $this->layout('allocation/view');
    }

    function test_rule()
    {
        $this->load->library('warehouse_lib');

        $warehouses = $this->warehouse_lib->getUserAllWarehouse($this->user->account_id, true);

        $this->data['warehouses'] = $warehouses;
        return $this->load->view('allocation/pages/test_rule', $this->data, true);
    }

    function edit($id = false)
    {
        return $this->rules($id);
    }



    function rules($id = false)
    {
        //echo $id; die;
        if ($id) {
            $filter = $this->allocation_lib->getByID($id);
            if (!$filter || $filter->user_id != $this->user->account_id) {
                $this->session->set_flashdata('error', 'Invalid Request');
                redirect('allocation/v/rules', true);
            }

            $this->data['edit_data'] = $filter;
            $this->data['edit_id'] = $id;
        }
        //**********code for smart pricing start************/
        $this->load->library('user_lib');
        $user = $this->user_lib->getByID($this->user->account_id);
        $this->load->library('plans_lib');
        $plans = $this->plans_lib->getPlanByName($user->pricing_plan);
        $plan_price_list = array();
        if(!empty($plans)){
            $plan_pricing = $this->plans_lib->getSmartPlanDetails($plans->id);
            if (!empty($plan_pricing)){
                foreach ($plan_pricing as $lp) {
                    $courier_type_weight = $lp->courier_type . '_' . $lp->weight . '_' . $lp->additional_weight;
                    if($lp->status=='1')
                        $plan_price_list[$courier_type_weight]= ucfirst(strtolower($lp->courier_type))." ".($lp->weight/1000)."kg";
                }
            }
        }
        $user_plan_flag='0';
        if(!empty($plans) && $plans->plan_type=='smart')
            $user_plan_flag='1';
           
        $user_filters = $this->allocation_lib->getUserfilters($this->user->account_id,false,$user_plan_flag);

        $this->data['plan_list'] = $plan_price_list;
        $this->data['user_plan'] = (array)$plans;
        //**********code for smart pricing end************/
        //pr($plan_pricing,1);
        $this->data['filters'] = $user_filters;
        $this->data['edit_id'] = $id;
        $this->data['user_plan_flag'] = $user_plan_flag;

        $this->load->library('courier_lib');
        $this->data['couriers'] = $this->courier_lib->userAvailableCouriers($this->user->account_id);

        return $this->load->view('allocation/pages/rules', $this->data, true);
    }

    function add_filter()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'name',
                'label' => 'Rule Name',
                'rules' => 'trim|required|max_length[100]'
            ),
            array(
                'field' => 'filter_id',
                'label' => 'Rule ID',
                'rules' => 'trim|numeric'
            ),
            array(
                'field' => 'filter[]',
                'label' => 'Rules',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'filter_type',
                'label' => 'Rule Type',
                'rules' => 'trim|required|in_list[or,and]'
            ),
            array(
                'field' => 'courier_priority_1',
                'label' => 'Courier Priority 1',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'courier_priority_2',
                'label' => 'Courier Priority 2',
                'rules' => 'trim'
            ),
            array(
                'field' => 'courier_priority_3',
                'label' => 'Courier Priority 3',
                'rules' => 'trim'
            ),
            array(
                'field' => 'courier_priority_4',
                'label' => 'Courier Priority 4',
                'rules' => 'trim'
            ),
            array('field' => 'courier_priority_4', 'label' => 'Courier Priority 4', 'rules' => 'trim'),
    array('field' => 'courier_priority_5', 'label' => 'Courier Priority 5', 'rules' => 'trim'),
    array('field' => 'courier_priority_6', 'label' => 'Courier Priority 6', 'rules' => 'trim'),
    array('field' => 'courier_priority_7', 'label' => 'Courier Priority 7', 'rules' => 'trim'),
    array('field' => 'courier_priority_8', 'label' => 'Courier Priority 8', 'rules' => 'trim'),

        );

        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        $name = $this->input->post('name');
        $priority = $this->input->post('priority');
        $filter_id = $this->input->post('filter_id');
        $filters = $this->input->post('filter');
        $filter_type = $this->input->post('filter_type');
        $user_plan = $this->input->post('user_plan');
        
        $courier_priority_1 = $this->input->post('courier_priority_1');
        $courier_priority_2 = $this->input->post('courier_priority_2');
        $courier_priority_3 = $this->input->post('courier_priority_3');
        $courier_priority_4 = $this->input->post('courier_priority_4');
        $courier_priority_5 = $this->input->post('courier_priority_5');
$courier_priority_6 = $this->input->post('courier_priority_6');
$courier_priority_7 = $this->input->post('courier_priority_7');
$courier_priority_8 = $this->input->post('courier_priority_8');

        $rules = array();

        foreach ($filters as $filter) {
            if (empty($filter['field']) || empty($filter['condition']) || empty($filter['value'])) {
                $this->data['json'] = array('error' => 'All fileds are required');
                $this->layout(false, 'json');
                return;
            }
            $rules[] = $filter;
        }

        $save = array(
            'filter_name' => $name,
            'priority' => $priority,
            'filter_type' => $filter_type,
            'user_plan' => $user_plan,
            'user_id' => $this->user->account_id,
            'conditions' => base64_encode(json_encode($rules)),
            'courier_priority_1' => $courier_priority_1,
            'courier_priority_2' => $courier_priority_2,
            'courier_priority_3' => $courier_priority_3,
            'courier_priority_4' => $courier_priority_4,
            'courier_priority_5' => $courier_priority_5,
    'courier_priority_6' => $courier_priority_6,
    'courier_priority_7' => $courier_priority_7,
    'courier_priority_8' => $courier_priority_8,
        );
        if ($filter_id) {
            $edit_data = $this->allocation_lib->getByID($filter_id);
            if (empty($edit_data) || $edit_data->user_id != $this->user->account_id) {
                $this->data['json'] = array('error' => 'Invalid Request');
                $this->layout(false, 'json');
                return;
            }
            $this->allocation_lib->update($filter_id, $save);
        } else {
            $this->allocation_lib->create($save);
        }

        $this->data['json'] = array('success' => 'Rule created successfully');
        $this->layout(false, 'json');
        return;
    }

    function delete_rule()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'filter_id',
                'label' => 'Filter ID',
                'rules' => 'trim|numeric|required'
            ),
        );

        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        $filter_id = $this->input->post('filter_id');



        $filter_data = $this->allocation_lib->getByID($filter_id);
        if (empty($filter_data) || $filter_data->user_id != $this->user->account_id) {
            $this->data['json'] = array('error' => 'Invalid Request');
            $this->layout(false, 'json');
            return;
        }

        $this->allocation_lib->delete($filter_id);


        $this->data['json'] = array('success' => 'Record deleted successfully');
        $this->layout(false, 'json');
        return;
    }

    function make_test()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'order_id',
                'label' => 'Order ID',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'warehouse_id',
                'label' => 'Warehouse',
                'rules' => 'trim|required|numeric'
            ),
        );

        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        $order_id = $this->input->post('order_id');
        $warehouse_id = $this->input->post('warehouse_id');

        $this->load->library('orders_lib');

        $order = $this->orders_lib->getByUserOrderID($this->user->account_id, $order_id);

        if (empty($order)) {
            $this->data['json'] = array('error' => 'Order not found');
            $this->layout(false, 'json');
            return;
        }

        $order_products = $this->orders_lib->getOrderProductsGrouped($order->id);

        $order->order_products_grouped = $order_products->product_name;
        $order->order_sku_grouped = $order_products->product_sku;

        $this->load->library('warehouse_lib');
        $warehouse = $this->warehouse_lib->getByID($warehouse_id);

        if (empty($warehouse)) {
            $this->data['json'] = array('error' => 'Warehouse not found');
            $this->layout(false, 'json');
            return;
        }

        $allocation = new Allocation_lib();

        $allocation->setUserID($order->user_id);

        $allocation->setProductName($order->order_products_grouped);
        $allocation->setProductSKU($order->order_sku_grouped);

        $allocation->setPaymentMode($order->order_payment_type);
        $allocation->setOrderAmount($order->order_amount);

        $allocation->setPickupPincode($warehouse->zip);
        $allocation->setDeliveryPincode($order->shipping_zip);

        $allocation->setWeight($order->package_weight);
        $allocation->setLength($order->package_length);
        $allocation->setBreadth($order->package_breadth);
        $allocation->setHeight($order->package_height);

        $matching_rule = $allocation->getMathchingRule();

        if (!$matching_rule) {
            $this->data['json'] = array('error' => 'No rule matched');
            $this->layout(false, 'json');
            return;
        }

        $this->data['json'] = array('success' => $matching_rule->filter_name);
        $this->layout(false, 'json');
        return;
    }

    function change_status()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'rule_id',
                'label' => 'Rule ID',
                'rules' => 'trim|numeric|required'
            ),
            array(
                'field' => 'status',
                'label' => 'Status',
                'rules' => 'trim|numeric|in_list[0,1]'
            ),
        );

        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        $rule_id = $this->input->post('rule_id');
        $status = $this->input->post('status');



        $filter_data = $this->allocation_lib->getByID($rule_id);
        if (empty($filter_data) || $filter_data->user_id != $this->user->account_id) {
            $this->data['json'] = array('error' => 'Invalid Request');
            $this->layout(false, 'json');
            return;
        }

        $save = array(
            'status' => $status,
        );

        $this->allocation_lib->update($rule_id, $save);


        $this->data['json'] = array('success' => 'Status Changed successfully');
        $this->layout(false, 'json');
        return;
    }
}
