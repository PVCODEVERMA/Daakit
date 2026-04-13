<?php

namespace App\Lib\Pricing;

use App\Lib\Pricing\Price;
use App\Lib\Pricing\Landing;

class PlanPrice extends Price
{
    protected $plan;
    protected $plan_id;
    protected $courier;
    protected $base;
    protected $mode = 'fwd';

    function __construct($plan_id, $courier_id, $mode = 'fwd')
    {
        parent::__construct();
        $this->plan_id = $plan_id;
        $this->courier = $courier_id;
        $this->mode = $mode;

        $this->base = new Landing($courier_id, $mode);

        $this->setPlan();
        $this->getPlanPrice();
    }

    private function setPlan()
    {
        if (!$this->plan_id)
            return false;

        $this->CI->load->library('plans_lib');
        $plan = $this->CI->plans_lib->getByID($this->plan_id);

        if (empty($plan))
            return false;

        $this->plan = $plan;
    }

    function getPlanPrice()
    {
        if (!$this->plan_id)
            return false;

        $this->CI->load->library('plans_lib');
        $price = $this->CI->plans_lib->getPlanDetailsByCourierAndType($this->plan_id, $this->courier, $this->mode);
        if (empty($price))
            return false;

        $this->setZone1Price($price->zone1);
        $this->setZone2Price($price->zone2);
        $this->setZone3Price($price->zone3);
        $this->setZone4Price($price->zone4);
        $this->setZone5Price($price->zone5);
        $this->setMinCod($price->min_cod);
        $this->setCodPercent($price->cod_percent);
    }

    function getZone1Margin()
    {
        return $this->zone1_price;
    }

    function getZone2Margin()
    {
        return $this->zone2_price;
    }

    function getZone3Margin()
    {
        return $this->zone3_price;
    }

    function getZone4Margin()
    {
        return $this->zone4_price;
    }

    function getZone5Margin()
    {
        return $this->zone5_price;
    }

    function getCodMargin()
    {
        return $this->min_cod;
    }

    function getCodPercentMargin()
    {
        return $this->cod_percent;
    }

    function getZone1Price()
    {
        return round($this->base->getZone1Price() + $this->zone1_price, 2);
    }

    function getZone2Price()
    {
        return round($this->base->getZone2Price() + $this->zone2_price, 2);
    }

    function getZone3Price()
    {
        return round($this->base->getZone3Price() + $this->zone3_price, 2);
    }

    function getZone4Price()
    {
        return round($this->base->getZone4Price() + $this->zone4_price, 2);
    }

    function getZone5Price()
    {
        return round($this->base->getZone5Price() + $this->zone5_price, 2);
    }

    function getMinCod()
    {
        return round($this->base->getMinCod() + $this->min_cod, 2);
    }

    function getCodPercent()
    {
        return round($this->base->getCodPercent() + $this->cod_percent, 2);
    }
}
