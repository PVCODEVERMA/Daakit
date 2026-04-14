<?php

namespace App\Lib\Pricing;

use App\Lib\Pricing\Price;

class Landing extends Price
{
    protected $courier;
    protected $mode = 'fwd';

    function __construct($courier_id, $mode = 'fwd')
    {
        parent::__construct();
        $this->courier = $courier_id;
        $this->mode = $mode;

        $this->loadPricing();
    }

    function loadPricing()
    {
        $this->CI->load->library('plans_lib');
        $landing = $this->CI->plans_lib->getLandingByCourierAndType($this->courier, $this->mode);
        if (empty($landing))
            return false;

        $this->setZone1Price($landing->zone1);
        $this->setZone2Price($landing->zone2);
        $this->setZone3Price($landing->zone3);
        $this->setZone4Price($landing->zone4);
        $this->setZone5Price($landing->zone5);
        $this->setMinCod($landing->min_cod);
        $this->setCodPercent($landing->cod_percent);
    }
}
