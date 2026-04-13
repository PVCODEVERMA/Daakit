<?php

defined('BASEPATH') or exit('No direct script access allowed');

use \App\Lib\Pricing\PlanPrice;

class Plans extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('admin/allocation_lib');
        $this->load->library('plans_lib');
        $this->userHasAccess('plans_view');

        $this->order_type = 'ecom';
    }

    function index()
    {
        self::v();
    }

    function v($page = 'plans', $page_no = 0, $courier_id = false)
    {
        $inner_content = '';
        switch ($page) {
            case 'plans':
                $inner_content = $this->plans_list();
                break;
            case 'add_plan':
                $inner_content = $this->add_plan($page_no);
                break;
            case 'add_pricing':
                $inner_content = $this->add_pricing($page_no);
                break;
            case 'landing':
                $inner_content = $this->landing($page_no);
                break;
            case 'view_pricing':
                $inner_content = $this->view_pricing($page_no);
                break;
            case 'copy_plan':
                $inner_content = $this->copy_plan($page_no);
                break;
            case 'plan_users':
                $inner_content = $this->plan_users();
                break;
            case 'add_pricing_custom':
                $inner_content = $this->add_pricing_custom($page_no);
                break;
            case 'view_pricing_custom':
                $inner_content = $this->view_pricing_custom($page_no);
                break;
            case 'add_custom_rules':
                $inner_content = $this->add_custom_rules($page_no, $courier_id);
                break;
            case 'rules':
                $inner_content = $this->rules($page_no, $courier_id);
                break;
            case 'add_rule':
                $inner_content = $this->add_rule();
                break;
            case 'edit_rule':
                $inner_content = $this->edit_rule($page_no, $courier_id);
                break;
            case 'import_pricing':
                $inner_content = $this->import_pricing();
                break;
            case 'upload':
                $inner_content = $this->upload($page_no);
                break;
            case 'actual_landing':
                $inner_content = $this->actual_landing($page_no);
                break;
            case 'export_plans':
                $inner_content = $this->export_plans();
                break;
            case 'export_user_plans_count':
                $inner_content = $this->export_user_plans_count();
                break;
            case 'export_margin':
                $inner_content = $this->export_margin();
                break;
            case 'export_landing':
                $inner_content = $this->export_landing();
                break;
            default:
        }
        $this->data['inner_content'] = $inner_content;
        $this->data['view_page'] = $page;
        $this->layout('plans/index');
    }

    function plans_list($plan_type="")
    {       
        $filter = $this->input->get('filter');
        $plan_type=$filter['plan_type'];
        $this->userHasAccess('plans_view_price');
        $plans = $this->plans_lib->getAllPlans($plan_type);

        $this->data['plans'] = $plans;
        $this->data['plan_filter'] = $plan_type;
        return $this->load->view('admin/plans/pages/plans', $this->data, true);
    }
    function export_plans()
    {
        $filter = $this->input->get('filter');
        $plan_type='standard';//$filter['plan_type'];
        $this->userHasAccess('plans_view_price');
        $userPlanDetails = $this->plans_lib->getUserPlanDetailByPlanType($plan_type);
        $filename = 'user_plans_' . date('Ymdhis') . rand(1111, 9999) . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array_map('strtoupper',array("Seller ID", "Company", "Seller", "Plan Type" ,"Plan ID", "Plan"));
        fputcsv($file, $header);

        foreach ($userPlanDetails as $plan) {
            $row = array(
                $plan->seller_id,
                $plan->company,
                $plan->seller,
                $plan->plan_type,
                $plan->plan_id,
                $plan->plan
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

    function export_user_plans_count()
    {
        $this->userHasAccess('plans_view_price');
        $userPlanDetails = $this->plans_lib->getUserCountPlanDetail();
        $filename = 'user_plans_count_' . time() . rand(1111, 9999) . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = ($userPlanDetails[0]) ? array_map('strtoupper',array_keys((array)$userPlanDetails[0])) : '';
        fputcsv($file, $header);
        foreach ($userPlanDetails as $plan) {
            $row = array(
                $plan->plan_id,
                $plan->plan,
                $plan->plan_type,
                $plan->total_users_count
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }
    function export_margin()
    {
        $marginPlanDetails = $this->plans_lib->getMarginPlanDetails();
        $filename = 'export_margin_' . date('Ymdhis') . rand(1111, 9999) . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = !empty($marginPlanDetails[0]) ? array_map('strtoupper',array_keys((array)$marginPlanDetails[0])) : '';
        fputcsv($file, $header);
        foreach ($marginPlanDetails as $plan) {
            fputcsv($file, (array)$plan);
        }
        fclose($file);
        exit;
    }
    function export_landing()
    {
        $landingPlanDetails = $this->plans_lib->getLandingPlanDetails();
        $filename = 'export_landing_' . date('Ymdhis') . rand(1111, 9999) . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = !empty($landingPlanDetails[0]) ? array_map('strtoupper',array_keys($landingPlanDetails[0])) : '';
        fputcsv($file, $header);
        foreach ($landingPlanDetails as $plan) {
            fputcsv($file,$plan);
        }
        fclose($file);
        exit;
    }
    function plan_users()
    {
        $this->userHasAccess('plan_users_count');
        $plans = $this->plans_lib->getUserCountByPlan();

        $this->data['plans'] = $plans;
        return $this->load->view('admin/plans/pages/plan_users', $this->data, true);
    }

    function add_plan($id = false)
    {
        $this->userHasAccess('plans_create');

        if ($id) {
            $plan = $this->plans_lib->getByID($id);
            if (empty($plan)) {
                $this->session->set_flashdata('error', 'Plan not found');
                redirect('admin/plans', true);
            }
            $this->data['plan_details'] = $plan;
        }
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'name',
                'label' => 'Plan Name',
                'rules' => 'trim|required|min_length[3]|max_length[20]'
            ),
            array(
                'field' => 'plan_type',
                'label' => 'Plan Type',
                'rules' => 'trim|in_list[standard,per_dispatch,smart]'
            )
        );

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run()) {
            $save = array(
                'plan_name' => $this->input->post('name'),
                'plan_type' => strtolower($this->input->post('plan_type'))
            );

            if ($id) {
                $this->plans_lib->updatePlan($id, $save);
            } else {
                $this->plans_lib->createPlan($save);
            }

            $this->session->set_flashdata('success', 'Details added successfully');
            redirect('admin/plans', true);
        } else {
            $this->data['error'] = validation_errors();
        }

        $plans = $this->plans_lib->getAllPlans();

        $this->data['plans'] = $plans;

        return $this->load->view('admin/plans/pages/add_plan', $this->data, true);
    }

    function add_pricing($plan_id = false)
    {
        $this->userHasAccess('plans_create');

        if (!$plan_id) {
            $this->session->set_flashdata('error', 'Please select a plan');
            redirect('admin/plans', true);
        }

        $plan = $this->plans_lib->getByID($plan_id);
        if (empty($plan) || !in_array($plan->plan_type, array('standard','per_dispatch'))) {
            $this->session->set_flashdata('error', 'Plan not found');
            redirect('admin/plans', true);
        }

        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->listAllCouriers($this->order_type);

        array_multisort(array_column($couriers, 'name'), SORT_ASC, $couriers);

        $this->data['couriers'] = $couriers;

        $config = array();

        foreach ($couriers as $c) {
            for ($i = 1; $i <= 5; $i++) {
                $config[] = array(
                    'field' => "pricing[{$c->id}][fwd][z{$i}]",
                    'label' => $c->name . ' FWD Z' . $i,
                    'rules' => 'trim|required|numeric'
                );
                $config[] = array(
                    'field' => "pricing[{$c->id}][rto][z{$i}]",
                    'label' => $c->name . ' RTO Z' . $i,
                    'rules' => 'trim|required|numeric'
                );
                $config[] = array(
                    'field' => "pricing[{$c->id}][weight][z{$i}]",
                    'label' => $c->name . ' Add. Weight Z' . $i,
                    'rules' => 'trim|required|numeric'
                );
            }
            $config[] = array(
                'field' => "pricing[{$c->id}][fwd][min_cod]",
                'label' => $c->name . ' MIN COD',
                'rules' => 'trim|required|numeric'
            );
            $config[] = array(
                'field' => "pricing[{$c->id}][fwd][cod_percent]",
                'label' => $c->name . ' COD Percent',
                'rules' => 'trim|required|numeric'
            );
        }

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run()) {
            $pricing = $this->input->post('pricing');

            //all correct now save to table
            foreach ($pricing as $courier_id => $price) {
                foreach ($price as $type => $zone) {
                    //check if records exists
                    $save = array(
                        'plan_id' => $plan_id,
                        'courier_id' => $courier_id,
                        'type' => $type,
                        'zone1' => round($zone['z1'], 2),
                        'zone2' => round($zone['z2'], 2),
                        'zone3' => round($zone['z3'], 2),
                        'zone4' => round($zone['z4'], 2),
                        'zone5' => round($zone['z5'], 2),
                        'min_cod' => (!empty($zone['min_cod']) ? round($zone['min_cod'], 2) : '0'),
                        'cod_percent' => (!empty($zone['cod_percent']) ? round($zone['cod_percent'], 2) : '0'),
                    );
                    if ($existing = $this->plans_lib->getPlanDetailsByCourierAndType($plan_id, $courier_id, $type)) {
                        $this->plans_lib->updatePrice($existing->id, $save);
                    } else {
                        //insert this record
                        $this->plans_lib->createPrice($save);
                    }
                }
            }
            $this->session->set_flashdata('success', 'Details Updated');
            redirect('admin/plans', true);
        } else {
            $this->data['error'] = validation_errors();
        }

        $this->data['plan_details'] = $plan;
        $this->data['plan_type'] = $plan->plan_type;

        $plan_pricing = $this->plans_lib->getPlanDetails($plan_id);

        $plan_price_list = array();

        if (!empty($plan_pricing))
            foreach ($plan_pricing as $lp) {
                $plan_price_list[$lp->courier_id][$lp->type] = (array)$lp;
            }

        $this->data['landing_price'] = $plan_price_list;

        return $this->load->view('admin/plans/pages/add_pricing', $this->data, true);
    }

    function add_pricing_custom($plan_id = false)
    {
        $this->userHasAccess('plans_create');

        if (!$plan_id) {
            $this->session->set_flashdata('error', 'Please select a plan');
            redirect('admin/plans', true);
        }

        $plan = $this->plans_lib->getByID($plan_id);
        if (empty($plan) || $plan->plan_type != 'smart') {
            $this->session->set_flashdata('error', 'Plan not found');
            redirect('admin/plans', true);
        }

        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->getCouriersByWeightSlabAndCourierType($this->order_type);

        $this->data['couriers'] = $couriers;

        $config = array();

        foreach ($couriers as $c) {
            $courier_type_weight = $c->courier_type . '_' . $c->weight . '_' . $c->additional_weight;
            for ($i = 1; $i <= 5; $i++) {
                $config[] = array(
                    'field' => "pricing[$courier_type_weight][fwd][z{$i}]",
                    'label' => ucwords($c->courier_type) . ' ' . $c->weight . ' FWD Z' . $i,
                    'rules' => 'trim|required|numeric'
                );
                $config[] = array(
                    'field' => "pricing[$courier_type_weight][rto][z{$i}]",
                    'label' => ucwords($c->courier_type) . ' ' . $c->weight . ' RTO Z' . $i,
                    'rules' => 'trim|required|numeric'
                );
                $config[] = array(
                    'field' => "pricing[$courier_type_weight][weight][z{$i}]",
                    'label' => ucwords($c->courier_type) . ' ' . $c->weight . ' Add. Weight Z' . $i,
                    'rules' => 'trim|required|numeric'
                );
            }
            $config[] = array(
                'field' => "pricing[$courier_type_weight][fwd][min_cod]",
                'label' => ucwords($c->courier_type) . ' ' . $c->weight . ' MIN COD',
                'rules' => 'trim|required|numeric'
            );
            $config[] = array(
                'field' => "pricing[$courier_type_weight][fwd][cod_percent]",
                'label' => ucwords($c->courier_type) . ' ' . $c->weight . ' COD Percent',
                'rules' => 'trim|required|numeric'
            );
        }

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run()) {
            $pricing = $this->input->post('pricing');

            $this->plans_lib->deleteSmartPrice($plan_id, 0);

            //all correct now save to table
            foreach ($pricing as $courier_id => $price) {
                $courier_id = explode('_', $courier_id);
                $courier_type = $courier_id[0];
                $weight = (!empty($courier_id[1])) ? $courier_id[1] : 0;
                $additional_weight = (!empty($courier_id[2])) ? $courier_id[2] : 0;

                foreach ($price as $type => $zone) {
                    //check if records exists
                    $save = array(
                        'plan_id' => $plan_id,
                        'courier_id' => 0,
                        'type' => $type,
                        'zone1' => round($zone['z1'], 2),
                        'zone2' => round($zone['z2'], 2),
                        'zone3' => round($zone['z3'], 2),
                        'zone4' => round($zone['z4'], 2),
                        'zone5' => round($zone['z5'], 2),
                        'min_cod' => (!empty($zone['min_cod']) ? round($zone['min_cod'], 2) : '0'),
                        'cod_percent' => (!empty($zone['cod_percent']) ? round($zone['cod_percent'], 2) : '0'),
                        'courier_type' => $courier_type,
                        'weight' => $weight,
                        'additional_weight' => $additional_weight,
                        'volumetric_divisor' => 5000,
                        'status' => (!empty($zone['status']) && ($zone['status'] == '1')) ? '1' : '0',
                    );

                    //insert this record
                    $this->plans_lib->createPrice($save);
                }
            }
            $this->session->set_flashdata('success', 'Details Updated');
            redirect('admin/plans', true);
        } else {
            $this->data['error'] = validation_errors();

            if (!empty($this->input->post('pricing')))
                $this->data['pricing'] = $this->input->post('pricing');
        }

        $this->data['plan_details'] = $plan;

        $plan_pricing = $this->plans_lib->getSmartPlanDetails($plan_id);

        $plan_price_list = array();

        if (!empty($plan_pricing))
            foreach ($plan_pricing as $lp) {
                $courier_type_weight = $lp->courier_type . '_' . $lp->weight . '_' . $lp->additional_weight;
                $plan_price_list[$courier_type_weight][$lp->type] = (array)$lp;
            }

        $this->data['landing_price'] = $plan_price_list;

        return $this->load->view('admin/plans/pages/add_pricing_custom', $this->data, true);
    }

    function landing()
    {
        $this->userHasAccess('plans_edit_landing');

        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->listAllCouriers($this->order_type);

        array_multisort(array_column($couriers, 'name'), SORT_ASC, $couriers);

        $this->data['couriers'] = $couriers;

        $config = array();

        foreach ($couriers as $c) {
            for ($i = 1; $i <= 5; $i++) {
                $config[] = array(
                    'field' => "pricing[{$c->id}][fwd][z{$i}]",
                    'label' => $c->name . ' FWD Z' . $i,
                    'rules' => 'trim|required|numeric|greater_than_equal_to[0]'
                );
                $config[] = array(
                    'field' => "pricing[{$c->id}][rto][z{$i}]",
                    'label' => $c->name . ' RTO Z' . $i,
                    'rules' => 'trim|required|numeric|greater_than_equal_to[0]'
                );
                $config[] = array(
                    'field' => "pricing[{$c->id}][weight][z{$i}]",
                    'label' => $c->name . ' Add. Weight Z' . $i,
                    'rules' => 'trim|required|numeric|greater_than_equal_to[0]'
                );
            }
            $config[] = array(
                'field' => "pricing[{$c->id}][fwd][min_cod]",
                'label' => $c->name . ' MIN COD',
                'rules' => 'trim|required|numeric|greater_than_equal_to[0]'
            );
            $config[] = array(
                'field' => "pricing[{$c->id}][fwd][cod_percent]",
                'label' => $c->name . ' COD Percent',
                'rules' => 'trim|required|numeric|greater_than_equal_to[0]'
            );
        }

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run()) {
            $pricing = $this->input->post('pricing');

            //all correct now save to table
            foreach ($pricing as $courier_id => $price) {
                foreach ($price as $type => $zone) {
                    //check if records exists
                    $save = array(
                        'courier_id' => $courier_id,
                        'type' => $type,
                        'zone1' => round($zone['z1'], 2),
                        'zone2' => round($zone['z2'], 2),
                        'zone3' => round($zone['z3'], 2),
                        'zone4' => round($zone['z4'], 2),
                        'zone5' => round($zone['z5'], 2),
                        'min_cod' => (!empty($zone['min_cod']) ? round($zone['min_cod'], 2) : '0'),
                        'cod_percent' => (!empty($zone['cod_percent']) ? round($zone['cod_percent'], 2) : '0'),
                    );
                    if ($existing = $this->plans_lib->getLandingByCourierAndType($courier_id, $type)) {
                        $this->plans_lib->updateLandingPrice($existing->id, $save);
                    } else {
                        //insert this record
                        $this->plans_lib->createLandingPrice($save);
                    }
                }
            }
            $this->data['success'] = 'Pricing updated.';
        } else {
            $this->data['error'] = validation_errors();
        }

        $landing_price = $this->plans_lib->getAllLandingPrice();

        $landing_price_list = array();

        if (!empty($landing_price))
            foreach ($landing_price as $lp) {
                $landing_price_list[$lp->courier_id][$lp->type] = (array)$lp;
            }

        $this->data['landing_price'] = $landing_price_list;

        return $this->load->view('admin/plans/pages/add_landing', $this->data, true);
    }

    function view_pricing($plan_id = false)
    {
        $this->userHasAccess('plans_view_price');

        if (!$plan_id) {
            $this->session->set_flashdata('error', 'Please select a plan');
            redirect('admin/plans', true);
        }

        $plan = $this->plans_lib->getByID($plan_id);
        if (empty($plan) || !in_array($plan->plan_type, array('standard','per_dispatch'))) {
            $this->session->set_flashdata('error', 'Plan not found');
            redirect('admin/plans', true);
        }

        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->listAllCouriers($this->order_type);

        array_multisort(array_column($couriers, 'name'), SORT_ASC, $couriers);
        
        $this->data['couriers'] = $couriers;

        $this->data['plan_details'] = $plan;

        $landing_price_list = array();

        foreach ($couriers as $courier) {
                $landing_price_list[$courier->id]['fwd'] = new \App\Lib\Pricing\PlanPrice($plan->id, $courier->id, 'fwd');
                $landing_price_list[$courier->id]['rto'] = new \App\Lib\Pricing\PlanPrice($plan->id, $courier->id, 'rto');
                $landing_price_list[$courier->id]['weight'] = new \App\Lib\Pricing\PlanPrice($plan->id, $courier->id, 'weight');
        }

        $this->data['landing_price'] = $landing_price_list;

        return $this->load->view('admin/plans/pages/view_pricing', $this->data, true);
    }

    function view_pricing_custom($plan_id = false)
    {
        $this->userHasAccess('plans_view_price');

        if (!$plan_id) {
            $this->session->set_flashdata('error', 'Please select a plan');
            redirect('admin/plans', true);
        }

        $plan = $this->plans_lib->getByID($plan_id);
        if (empty($plan) || $plan->plan_type != 'smart') {
            $this->session->set_flashdata('error', 'Plan not found');
            redirect('admin/plans', true);
        }

        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->getCouriersByWeightSlabAndCourierType($this->order_type);

        $this->data['couriers'] = $couriers;

        $this->data['plan_details'] = $plan;

        $landing_price_list = array();

        foreach ($couriers as $courier) {
            $courier_type_weight = $courier->courier_type . '_' . $courier->weight . '_' . $courier->additional_weight;
        }

        $this->data['landing_price'] = $landing_price_list;

        return $this->load->view('admin/plans/pages/view_pricing_custom', $this->data, true);
    }

    function copy_plan($id = false)
    {
        $this->userHasAccess('plans_create');

        if (!$id) {
            $this->session->set_flashdata('error', 'Please select a plan');
            redirect('admin/plans', true);
        }

        $plan = $this->plans_lib->getByID($id);
        if (empty($plan)) {
            $this->session->set_flashdata('error', 'Plan not found');
            redirect('admin/plans', true);
        }
        $this->data['plan_details'] = $plan;

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'name',
                'label' => 'Plan Name',
                'rules' => 'trim|required|min_length[3]|max_length[20]'
            ),
            array(
                'field' => 'plan_type',
                'label' => 'Plan Type',
                'rules' => 'trim|in_list[standard,per_dispatch,smart]'
            )
        );

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run()) {
            $save = array(
                'plan_name' => $this->input->post('name'),
                'plan_type' => $this->input->post('plan_type')
            );

            $plan_id = $this->plans_lib->createPlan($save);

            $copy_from_pricing = $this->plans_lib->getPlanDetails($id);

            if (!empty($copy_from_pricing))
                foreach ($copy_from_pricing as $lp) {
                    $save = array(
                        'plan_id' => $plan_id,
                        'courier_id' => $lp->courier_id,
                        'type' => $lp->type,
                        'zone1' => $lp->zone1,
                        'zone2' => $lp->zone2,
                        'zone3' => $lp->zone3,
                        'zone4' => $lp->zone4,
                        'zone5' => $lp->zone5,
                        'min_cod' => $lp->min_cod,
                        'cod_percent' => $lp->cod_percent,
                    );
                    $this->plans_lib->createPrice($save);
                }

            $this->session->set_flashdata('success', 'Plan copied successfully');
            redirect('admin/plans', true);
        } else {
            $this->data['error'] = validation_errors();
        }

        $plans = $this->plans_lib->getAllPlans();

        $this->data['plans'] = $plans;

        return $this->load->view('admin/plans/pages/copy_plan', $this->data, true);
    }

    function add_custom_rules($plan_id = false)
    {
        redirect('admin/plans', true);
        
        $this->userHasAccess('plans_create');

        if (empty($plan_id)) {
            $this->session->set_flashdata('error', 'Plan not found');
            redirect('admin/plans', true);
        }

        $plan = $this->plans_lib->getByID($plan_id);
        if (empty($plan) || $plan->plan_type != 'smart') {
            $this->session->set_flashdata('error', 'Plan not found');
            redirect('admin/plans', true);
        }

        $this->data['plan_details'] = $plan;

        if ($plan_id) {
            $filter = $this->allocation_lib->getUserfiltersByPlanId($plan_id);
            if ($filter) {
                $edit_data = array();
                foreach ($filter as $courier) {
                    $edit_data[$courier->filter_name][$courier->zone] = $courier;
                }

                $this->data['edit_data'] = $edit_data;
            }
        }

        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->getCouriersByWeightSlabAndCourierType($this->order_type);

        $couriersArr = [];
        if (!empty($couriers)) {
            foreach ($couriers as $courier) {
                $couriersArr[$courier->courier_type . '_' . $courier->weight . '_' . $courier->additional_weight] = ucwords($courier->courier_type) . ' ' . round($courier->weight/1000, 2) . ' kg';
            }
        }

        $this->data['couriersArr'] = $couriersArr;
        
        $this->data['plan_id'] = $plan_id;

        $this->load->library('courier_lib');
        $this->data['couriers'] = $this->courier_lib->showingToUsers($this->order_type);

        return $this->load->view('admin/plans/pages/add_custom_rules', $this->data, true);
    }

    function add_custom_filter()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'plan_id',
                'label' => 'Plan ID',
                'rules' => 'trim|required|numeric'
            )
        );

        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        $plan_id = $this->input->post('plan_id');
        $courier_priority = $this->input->post('courier_priority');

        $this->allocation_lib->deleteByPlanId($plan_id);

        $save = array();
        foreach ($courier_priority as $key => $zone) {
            foreach ($zone as $z => $priority) {
                if(!empty($priority[1]) || !empty($priority[2]) || !empty($priority[3]) || !empty($priority[4])) {
                    $save[] = array(
                        'plan_id' => $plan_id,
                        'filter_name' => $key,
                        'priority' => '1',
                        'filter_type' => 'and',
                        'conditions' => base64_encode(json_encode($priority)),
                        'courier_priority_1' => $priority[1],
                        'courier_priority_2' => $priority[2],
                        'courier_priority_3' => $priority[3],
                        'courier_priority_4' => $priority[4],
                        'courier_priority_5' => $priority[5],
                        'courier_priority_6' => $priority[6],
                        'courier_priority_7' => $priority[7],
                        'courier_priority_8' => $priority[8],
                        'zone' => $z,
                        'created' => time(),
                        'modified' => time()
                    );
                }
            }
        }

        if (empty($save)) {
            $this->data['json'] = array('error' => 'Empty Request');
            $this->layout(false, 'json');
            return;
        }

        $this->allocation_lib->batchInsert($save);

        $this->data['json'] = array('success' => 'Rule created successfully');
        $this->layout(false, 'json');
        return;
    }

    function test_rule($seller_id = false)
    {
        $this->load->library('warehouse_lib');

        $warehouses = $this->warehouse_lib->getUserAllWarehouse($seller_id, true);

        $this->data['warehouses'] = $warehouses;
        return $this->load->view('admin/plans/pages/test_rule', $this->data, true);
    }

    function add_rule($seller_id = false, $id = false)
    {
        return $this->rules($seller_id, $id, 'add');
    }

    function edit_rule($seller_id = false, $id = false)
    {
        return $this->rules($seller_id, $id, 'edit');
    }

    function rules($seller_id = false, $id = false, $type = 'view')
    {
        $this->userHasAccess('smart_plans_allocation_rule');

        $this->load->library('user_lib');
        $all_users = $this->user_lib->getAllUserList();
        $allUser = [];
        if(!empty($all_users)) {
            foreach ($all_users as $value) {
                $allUser[$value->id] = $value->id . ' - ' . $value->company_name . ' ( ' . $value->user_fname . ' ' . $value->user_lname . ' )';
            }
        }
        $this->data['all_users'] = $allUser;

        $user = [];
        if ($seller_id) {
            $user = $this->user_lib->getByID($seller_id);
            if (empty($user) || $user->parent_id != '0') {
                $this->session->set_flashdata('error', 'Something Wrong');
                redirect(base_url('admin/plans/v/rules'), true);
            }
        }

        $filter = $this->input->get('filter');
        $plan_id = '';
        if(!empty($filter) && (!empty($filter['seller_id']) || !empty($filter['plan_id']))) {
            $seller_id = $filter['seller_id'];
            $plan_id = $filter['plan_id'];
        }
        
        $this->load->library('admin/user_lib');
        $seller_details = '';
        if (!empty($filter['seller_id']))
            $seller_details = $this->user_lib->getUserListRules($seller_id);
        
        $this->data['users'] = $seller_details;

        $user_filters = $this->allocation_lib->getUserfilters($seller_id, false, $id, $plan_id);
        if ($seller_id != '' && $id != '' && empty($user_filters)) {
            $this->session->set_flashdata('error', 'Invalid Request');
            redirect('admin/plans/v/rules', true);
        }

        if ($id) {
            $filter = $this->allocation_lib->getByID($id);
            if (!$filter) {
                $this->session->set_flashdata('error', 'Invalid Request');
                redirect('admin/plans/v/rules', true);
            }

            $this->data['edit_data'] = $filter;

            if(!empty($filter->user_id)) {
                $selUser = [];
                $user_ids = explode(",", $filter->user_id);

                $sel_users = $this->user_lib->getUserListFilter($user_ids);
                if(!empty($sel_users)) {
                    foreach ($sel_users as $value) {
                        $selUser[$value->id] = $value->id . ' - ' . $value->company_name . ' ( ' . $value->user_fname . ' ' . $value->user_lname . ' )';
                    }
                    
                    $this->data['sel_users'] = $selUser;
                }
            }
        }

        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->getCouriersByWeightSlabAndCourierType($this->order_type);

        $couriersArr = [];
        $couriersArr[''] = 'Select';
        if (!empty($couriers)) {
            foreach ($couriers as $courier) {
                $courier_alias =isset(($courier->courier_alias)) ? " (".ucfirst($courier->courier_alias).")" : "";
                $couriersArr[$courier->courier_type . '_' . $courier->weight] = ucwords($courier->courier_type) . ' ' . round($courier->weight/1000, 2) . ' kg' .$courier_alias;
            }
        }

        $this->data['couriersArr'] = $couriersArr;

        $smart_plans = $this->plans_lib->getAllSmartPlans();
        if (!empty($smart_plans)) {
            foreach ($smart_plans as $smart_plan) {
                $plansArr[$smart_plan->id] = $smart_plan->plan_name;
            }
        }

        $this->data['plansArr'] = $plansArr;
        
        $this->data['filters'] = $user_filters;
        $this->data['edit_id'] = $id;

        $this->data['user'] = $user;
        $this->data['seller_id'] = ($seller_id) ? $seller_id : 0;
        $this->data['plan_id'] = $plan_id;

        $this->data['couriers'] = ($seller_id) ? $this->courier_lib->userAvailableCouriers($seller_id) : $this->courier_lib->showingToUsers($this->order_type);
//   couriersArr
        if (in_array($type, ['add','edit'])) {
            return $this->load->view('admin/plans/pages/edit_rules', $this->data, true);
        } else {
            return $this->load->view('admin/plans/pages/rules', $this->data, true);
        }
    }

    function add_filter()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'seller_id[]',
                'label' => 'Sellers',
                'rules' => 'trim|numeric'
            ),
            array(
                'field' => 'plan_id[]',
                'label' => 'Plans',
                'rules' => 'trim|numeric'
            ),
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
                'rules' => 'required'
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
            array(
                'field' => 'courier_priority_5',
                'label' => 'Courier Priority 5',
                'rules' => 'trim'
            ),
            array(
                'field' => 'courier_priority_6',
                'label' => 'Courier Priority 6',
                'rules' => 'trim'
            ),
            array(
                'field' => 'courier_priority_7',
                'label' => 'Courier Priority 7',
                'rules' => 'trim'
            ),
            array(
                'field' => 'courier_priority_8',
                'label' => 'Courier Priority 8',
                'rules' => 'trim'
            )
        );

        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run()) {
            $this->data['json'] = array('error' => strip_tags(validation_errors()));
            $this->layout(false, 'json');
            return;
        }

        $seller_id = ($this->input->post('seller_id')) ? implode(",", $this->input->post('seller_id')) : 0;
        $plan_id = ($this->input->post('plan_id')) ? implode(",", $this->input->post('plan_id')) : 0;
        $name = $this->input->post('name');
        $priority = $this->input->post('priority');
        $filter_id = $this->input->post('filter_id');
        $filters = $this->input->post('filter');
        $filter_type = $this->input->post('filter_type');

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

            if(in_array($filter['field'], ['zone'])) {
                $filter['value'] = ($filter['value']) ? implode(",", $filter['value']) : '';
                $rules[] = $filter;
            } else {
                $rules[] = $filter;
            }
        }

        $save = array(
            'filter_name' => $name,
            'priority' => $priority,
            'filter_type' => $filter_type,
            'user_id' => $seller_id,
            'plan_id' => $plan_id,
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
            if (empty($edit_data)) {
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
        if (empty($filter_data)) {
            $this->data['json'] = array('error' => 'Invalid Request');
            $this->layout(false, 'json');
            return;
        }

        $this->allocation_lib->delete($filter_id);

        $this->data['json'] = array('success' => 'Record deleted successfully');
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
        if (empty($filter_data)) {
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

    function import_pricing()
    {
        $this->userHasAccess('plans_create');

        if (!$this->plans_lib->uploadLandingPrice()) {
            $this->data['error'] = $this->plans_lib->get_error();
        } else {
            $this->session->set_flashdata('success', 'Pricing uploaded successfully');
            redirect('admin/plans/v/import_pricing', true);
        }
        
        return $this->load->view('admin/plans/pages/import_pricing', $this->data, true);
    }

    function check_pricing_with_landing()
    {
        $this->userHasAccess('plans_create');

        if (!$this->plans_lib->validatePricingOnly()) {
            echo json_encode([
                'status' => 'error',
                'message' => $this->plans_lib->get_error()
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'message' => 'All pricing is valid'
            ]);
        }
        exit;
    }

    function file_check()
    {
        $allowed_mime_types = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
        if (isset($_FILES['importFile']['name']) && $_FILES['importFile']['name'] != "") {
            $mime = get_mime_by_extension($_FILES['importFile']['name']);
            $fileAr = explode('.', $_FILES['importFile']['name']);
            $ext = end($fileAr);
            if (($ext == 'csv') && in_array($mime, $allowed_mime_types)) {
                return true;
            } else {
                $this->form_validation->set_message('file_check', 'Please select only CSV file to upload.');
                return false;
            }
        } else {
            $this->form_validation->set_message('file_check', 'Please select a CSV file to upload.');
            return false;
        }
    }

    function actual_landing() {
        $this->userHasAccess('plans_edit_landing');

        $this->load->library('courier_lib');
        $couriers = $this->courier_lib->listAllCouriers($this->order_type);

        array_multisort(array_column($couriers, 'name'), SORT_ASC, $couriers);

        $this->data['couriers'] = $couriers;

        $config = array();

        foreach ($couriers as $c) {
            for ($i = 1; $i <= 5; $i++) {
                $config[] = array(
                    'field' => "pricing[{$c->id}][fwd][z{$i}]",
                    'label' => $c->name . ' FWD Z' . $i,
                    'rules' => 'trim|required|numeric|greater_than_equal_to[0]'
                );
                $config[] = array(
                    'field' => "pricing[{$c->id}][rto][z{$i}]",
                    'label' => $c->name . ' RTO Z' . $i,
                    'rules' => 'trim|required|numeric|greater_than_equal_to[0]'
                );
                $config[] = array(
                    'field' => "pricing[{$c->id}][weight][z{$i}]",
                    'label' => $c->name . ' Add. Weight Z' . $i,
                    'rules' => 'trim|required|numeric|greater_than_equal_to[0]'
                );
            }
            $config[] = array(
                'field' => "pricing[{$c->id}][fwd][min_cod]",
                'label' => $c->name . ' MIN COD',
                'rules' => 'trim|required|numeric|greater_than_equal_to[0]'
            );
            $config[] = array(
                'field' => "pricing[{$c->id}][fwd][cod_percent]",
                'label' => $c->name . ' COD Percent',
                'rules' => 'trim|required|numeric|greater_than_equal_to[0]'
            );
        }

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run()) {
            $pricing = $this->input->post('pricing');

            //all correct now save to table
            foreach ($pricing as $courier_id => $price) {
                foreach ($price as $type => $zone) {
                    //check if records exists
                    $save = array(
                        'courier_id' => $courier_id,
                        'type' => $type,
                        'zone1' => round($zone['z1'], 2),
                        'zone2' => round($zone['z2'], 2),
                        'zone3' => round($zone['z3'], 2),
                        'zone4' => round($zone['z4'], 2),
                        'zone5' => round($zone['z5'], 2),
                        'min_cod' => (!empty($zone['min_cod']) ? round($zone['min_cod'], 2) : '0'),
                        'cod_percent' => (!empty($zone['cod_percent']) ? round($zone['cod_percent'], 2) : '0'),
                    );
                    if ($existing = $this->plans_lib->getActualLandingByCourierAndType($courier_id, $type)) {
                        $this->plans_lib->updateActualLandingPrice($existing->id, $save);
                    } else {
                        //insert this record
                        $this->plans_lib->createActualLandingPrice($save);
                    }
                }
            }
            $this->data['success'] = 'Pricing updated.';
        } else {
            $this->data['error'] = validation_errors();
        }

        $landing_price = $this->plans_lib->getAllActualLandingPrice();

        $landing_price_list = array();

        if (!empty($landing_price))
            foreach ($landing_price as $lp) {
                $landing_price_list[$lp->courier_id][$lp->type] = (array)$lp;
            }

        $this->data['landing_price'] = $landing_price_list;

        return $this->load->view('admin/plans/pages/add_actual_landing', $this->data, true);
    }
}
