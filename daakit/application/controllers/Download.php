<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Download extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    function index()
    {
    }



    function force()
    {
        $file = $this->input->get('file');
        $type = $this->input->get('type');
        if (empty($file))
            die();

        if (strstr($file, "amazonaws.com", true)) {
            redirect($file);
        }
        $url = parse_url($file);

        if (isset($url['scheme']) && ($url['scheme'] == 'https' || $url['scheme'] == 'http')) {
            redirect($file);
        }
        switch ($type) {
            case 'escalation':
                $type = 'assets/escalations/';
                break;
            case 'invoice':
                $type = 'assets/invoice/';
                break;
            default:
                $type = '';
        }

        $this->load->helper('download');
        force_download($type . $file, NULL, TRUE);
    }
}
