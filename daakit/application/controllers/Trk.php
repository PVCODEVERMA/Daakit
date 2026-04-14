<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Trk extends MY_Controller
{
    private $aftership;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('shipping_lib');
        header("X-Frame-Options: ALLOW");
        header("Content-Security-Policy: frame-ancestors '*'");
    }


    private function no_host_found()
    {
        echo $this->load->view('host-not-found', $this->data, true);
        exit;
    }

    function tracking($awb = false, $is_rto = false)
    {
        if (!$awb)
            return false;

        $this->load->library('user_agent');

        $user_id = false;
        if (!empty($this->aftership))
            $user_id = $this->aftership->user_id;

        $shipment = $this->shipping_lib->getTrackingData($awb, $is_rto, $user_id);
        if (empty($shipment))
            die('Sorry, the page you are looking for could not be found.');

           if(!empty($shipment->tracking) && !empty($shipment->courier->display_name) && (strtolower($shipment->courier->display_name) == 'smartr'))
           {
                $locationArr = array();
                $this->load->library('courier_lib');
                foreach($shipment->tracking as $loc)
                {
                    if(!empty($loc->location))
                    {
                        if(!empty($locationArr) && array_key_exists($loc->location, $locationArr)) {
                            $loc->location = $locationArr[$loc->location];
                        } else {
                            $locationname = $this->courier_lib->getLocationname($loc->location);
                            if(!empty($locationname))
                            {
                                $locationArr[$loc->location] = $locationname[0]->city_name.", ".$locationname[0]->state_name;
                                $loc->location = $locationname[0]->city_name.", ".$locationname[0]->state_name;
                            }
                        }
                    }
                }
           }

        $this->data['shipment'] = $shipment;
        $shipment_id = isset($shipment->shipment->id) ? $shipment->shipment->id : "";
        $this->data['aftership'] = $this->aftership;
        $order_status = isset($shipment->shipment->ship_status) ? $shipment->shipment->ship_status : "";
        $this->load->view('trk/tracking_page', $this->data);
    }

    function track_order()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'track_with',
                'label' => 'Track With',
                'rules' => 'trim|required|in_list[awb,order_id]'
            ),
        );

        if ($this->input->post('track_with') == 'awb') {
            $config[] = array(
                'field' => 'awb_number',
                'label' => 'AWB Number',
                'rules' => 'trim|required'
            );
        } else {
            $config[] = array(
                'field' => 'order_id',
                'label' => 'Order ID',
                'rules' => 'trim|required'
            );
            $config[] = array(
                'field' => 'mobile',
                'label' => 'Mobile Number',
                'rules' => 'trim|required|numeric|exact_length[10]'
            );
        }

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $track_with = $this->input->post('track_with');
            $awb = $this->input->post('awb_number');
            $order_id = $this->input->post('order_id');
            $mobile = $this->input->post('mobile');

            $user_id = false;
            if (!empty($this->aftership))
                $user_id = $this->aftership->user_id;

            $this->load->library('shipping_lib');
            if ($track_with == 'awb') {

                $shipment = $this->shipping_lib->getByAWB($awb, $user_id);
                if (!empty($shipment)) {
                    redirect('trk/' . $awb, true);
                } else {
                    $this->data['error'] = 'AWB number is invalid.';
                }
            } else {
                //get by order id and mobile
                $shipments = $this->shipping_lib->getByOrderNoMobile($order_id, $mobile, $user_id);
                if (!empty($shipments)) {
                    $shipments_count = count($shipments);
                    if ($shipments_count > 1) {
                        $this->data['shipments'] = $shipments;
                    } else {
                        redirect('trk/' . $shipments[0]->awb_number, true);
                    }
                } else {
                    $this->data['error'] = 'Order ID or mobile number is invalid.';
                }
            }
        } else {
            $this->data['error'] = validation_errors();
        }
        $this->data['aftership'] = $this->aftership;

        $this->load->view('trk/track_order', $this->data);
    }

    function tracking_ordernumber($order_id = false)
    {
        if (!$order_id)
        return false;

               // $user_id='63544';
               $user_id = false;
               if (!empty($this->aftership))
                   $user_id = $this->aftership->user_id;

                $this->load->library('shipping_lib');
                $shipments = $this->shipping_lib->getByOrderNo($order_id, $user_id);

                if (empty($shipments)){ 
                die('Sorry, the page you are looking for could not be found.');
                }

              
                if (!empty($shipments)) {
                  $shipments_count = count($shipments); 
                    if ($shipments_count > 1) { 
                        $this->data['shipments'] = $shipments;
                    } else {  
                        redirect('trk/' . $shipments[0]->awb_number, true);
                        //redirect('shipping/tracking/' . $shipments[0]->awb_number, true);
                    }
                } else {
                    $this->data['error'] = 'Order ID is invalid.';
                }
                $this->data['aftership'] = $this->aftership;
                $this->load->view('trk/track_order', $this->data);
  
    }
}
