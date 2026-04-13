<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pickups_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('pickups_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->pickups_model, $method)) {
            throw new Exception('Undefined method pickups_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->pickups_model, $method], $arguments);
    }

    function download_manifest($ids = false, $user_id = false)
    {
        if (empty($ids) || !$user_id)
            return false;

        if (is_numeric($ids))
            $ids = array($ids);

        if (!is_array($ids))
            return false;

        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => './temp',
            'mode' => 'utf-8',
            //'format' => [190, 236],
            'margin_left' => 10,
            'margin_top' => 20,
            'margin_right' => 10,
            'margin_bottom' => 10,
        ]);

        $this->CI->load->library('shipping_lib');
        $this->CI->load->library('courier_lib');

        $manifest_count = count($ids);
        $i = 1;
    
        foreach ($ids as $id) {
            // $manifest = $this->getByID($id);
            $manifest = $this->getShipmentidsByPickdata($id);
            if (empty($manifest))
                return false;

            if ($manifest->user_id != $user_id)
                return false;
           
            $courier = $this->CI->courier_lib->getByID($manifest->courier_id);

            $shipment_ids = $manifest->shipment_ids;
            $shipment_ids = explode(',', $shipment_ids);
          
            $manifest_data = array();
            $manifest_data['manifest'] = $manifest;
            $manifest_data['courier'] = $courier;
            $manifest_data['shipments'] = array();
            foreach ($shipment_ids as $shipment_id) {
                $shipment_data = $this->CI->shipping_lib->getShipmentData($shipment_id);
                if ($shipment_data->shipment->ship_status != 'cancelled')
                    $manifest_data['shipments'][] = $shipment_data;
            }

            $pdf_content = $this->CI->load->view('pickups/manifest', $manifest_data, true);

            $mpdf->WriteHTML($pdf_content);
            if ($i < $manifest_count)
                $mpdf->AddPage();

            $i++;
        }

        $file_name = date('YmdHis') . '-' . rand(10, 99) . '.pdf';

        $this->CI->load->library('s3');

        $directory = 'assets/manifest/';
        $mpdf->Output($directory . $file_name, 'F');
        $aws_file_name = $this->CI->s3->amazonS3Upload($file_name, $directory . $file_name, 'manifest');
        //$mpdf->Output();
        //unlink($directory . $file_name);

        return $aws_file_name;
    }
}
