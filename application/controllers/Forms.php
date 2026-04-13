<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Forms extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    function index()
    {
    }

    function success()
    {
        $this->load->view('ndr/ndr_response_success');
        return;
    }

    function ndr($shipment_id = false, $ndr_id = false)
    {

        $this->load->library('ndr_lib');
        $this->load->library('shipping_lib');
        $this->load->library('orders_lib');
        $ndr = $this->ndr_lib->getByID($ndr_id);
        if (empty($ndr) || $ndr->shipment_id != $shipment_id)
            die('No Records Found');

        $action = $this->ndr_lib->ndrCourierLastAction($ndr_id);

        $buyer_action = $this->ndr_lib->getBuyerLastAction($ndr_id);

        if (empty($action))
            die('No Records Found');

        if (!empty($buyer_action) && $buyer_action->attempt == $action->attempt)
            die('Your response has been already submitted');

        $shipment = $this->shipping_lib->getShipmentByID($shipment_id);

        if (empty($shipment))
            die('No Records Found');


        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'action',
                'label' => 'Action',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'remarks',
                'label' => 'Remarks',
                'rules' => 'trim|max_length[200]'
            ),
        );

        switch (strtolower($this->input->post('action'))) {
            case 're-attempt':
                $ndr_rules = array(
                    array(
                        'field' => 're_attempt_date',
                        'label' => 'Re-Attempt Date',
                        'rules' => 'trim|required|integer'
                    ),
                );
                $config = array_merge($config, $ndr_rules);
                break;
            case 'change address':
                $ndr_rules = array(
                    array(
                        'field' => 'customer_details_name',
                        'label' => 'Customer Name',
                        'rules' => 'trim|required|max_length[50]'
                    ),
                    array(
                        'field' => 'customer_details_address_1',
                        'label' => 'Customer Address 1',
                        'rules' => 'trim|required|min_length[10]|max_length[200]'
                    ),
                    array(
                        'field' => 'customer_details_address_2',
                        'label' => 'Customer Address 2',
                        'rules' => 'trim|max_length[200]'
                    ),
                );
                $config = array_merge($config, $ndr_rules);
                break;
            case 'change phone':
                $ndr_rules = array(
                    array(
                        'field' => 'customer_contact_phone',
                        'label' => 'Phone Number',
                        'rules' => 'trim|required|exact_length[10]|numeric'
                    )
                );
                $config = array_merge($config, $ndr_rules);
                break;
            default:
        }

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $action = $this->input->post('action');
            $remarks = $this->input->post('remarks');
            $re_attempt_date = $this->input->post('re_attempt_date');
            $customer_details_name = $this->input->post('customer_details_name');
            $customer_details_address_1 = $this->input->post('customer_details_address_1');
            $customer_details_address_2 = $this->input->post('customer_details_address_2');
            $customer_contact_phone = $this->input->post('customer_contact_phone');


            $save = array(
                'ndr_id' => $ndr_id,
                'action' => $action,
                'remarks' => $remarks,
                'source' => 'buyer',
                're_attempt_date' => $re_attempt_date,
                'customer_details_name' => $customer_details_name,
                'customer_details_address_1' => $customer_details_address_1,
                'customer_details_address_2' => $customer_details_address_2,
                'customer_contact_phone' => $customer_contact_phone,
            );

            $this->ndr_lib->AddNDRAction($save);

            redirect(base_url('forms/success'), true);
        } else {
            $this->data['error'] = validation_errors();
        }


        $this->data['ndr'] = $ndr;
        $this->data['action'] = $action;
        $this->data['shipment'] = $shipment;


        $this->load->view('ndr/ndr_response', $this->data);
    }
}
