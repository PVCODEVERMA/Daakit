<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Remittance extends User_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('remittance_lib');
    }

    function exportAWB($remittance_id = false) {
        if (!$remittance_id) {
            $this->session->set_flashdata('error', 'Details Missing');
            redirect(base_url('billing/v/cod_remittance'));
        }

        $remitance = $this->remittance_lib->getByID($remittance_id);
        if (empty($remitance) || $remitance->user_id != $this->user->account_id) {
            $this->session->set_flashdata('error', 'Invalid Access');
            redirect(base_url('billing/v/cod_remittance'));
        }

        $shipments = $this->remittance_lib->getShippingDetails($remittance_id);
        

        $filename = 'Remittance_' . $remittance_id . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Shipment ID", "Shipment Date", "Order ID", "Carrier Name", "AWB", "Amount", "Delivery Date");
        fputcsv($file, $header);
        foreach ($shipments as $shipment) {
            $row = array(
                $shipment->shipping_id,
                (!empty($shipment->shipping_created)) ? date('d-M-Y', $shipment->shipping_created) : '',
                $shipment->order_id,
                $shipment->courier_name,
                $shipment->awb_number,
                $shipment->order_amount,
                (!empty($shipment->delivered_time)) ? date('d-M-Y', $shipment->delivered_time) : ''
            );
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }

}
