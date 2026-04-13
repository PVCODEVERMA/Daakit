<?php

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Maxbanton\Cwh\Handler\CloudWatch;
use Monolog\Logger;
use Monolog\Formatter\JsonFormatter;

defined('BASEPATH') or exit('No direct script access allowed');

class Review extends MY_controller
{

    public function __construct()
    {
        parent::__construct();
        error_reporting(-1);
        ini_set('display_errors', 1);
    }

    function env()
    {
        $url = 'http://ordr.live/forms/ndr/57485/545';

        echo $url = $this->format_shopify_url($url);
        echo '<br/>';
        pr(explode('.', $url)[0]);

        if (!preg_match("/\.ordr\.live$/", $url))
            echo 'no';
        else
            echo 'yes';
    }

    //generate awb number
    function ship($shipment_id = false, $skip_inactive = false)
    {
        if ($skip_inactive)
            define('skip_inactive', 'yes');

        $this->load->library('shipping_lib');
        if (!$awb = $this->shipping_lib->processShipment($shipment_id)) {
            echo $this->shipping_lib->get_error();
        } else {
            pr($awb);
        }
    }
    function courier_tracking($awb_number = false, $rto_tracking = false)
    {
        ini_set('display_errors',1);
        if (!$awb_number)
            return false;

        define('print_tracking', 'yes');

        $this->load->library('shipping_lib');
        $shipment = $this->shipping_lib->getByAWB($awb_number);
        if (empty($shipment))
            die('shipment not found');

        $this->shipping_lib->getTrackingHistoryLive($shipment->id, $rto_tracking);
    }

    function generateInvoice($user_id = false)
    {
        if (!$user_id)
            return false;

        $this->load->library('generate_invoice');
        $invoice = new Generate_invoice(array('user_id' => $user_id, 'type' => 'shipment'));
        $invoice->generateInvoiceForUser();
        echo "Invoice successfully generated for user ID $user_id";
    }

}
