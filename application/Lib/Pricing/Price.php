<?php

namespace App\Lib\Pricing;

use App\Lib\BaseLib;

class Price extends BaseLib
{
    protected $zone1_price = '0';
    protected $zone2_price = '0';
    protected $zone3_price = '0';
    protected $zone4_price = '0';
    protected $zone5_price = '0';
    protected $min_cod = '0';
    protected $cod_percent = '0';

    function getZone1Price()
    {
        return $this->zone1_price;
    }

    function setZone1Price($value = '0')
    {
        $this->zone1_price = $value;
        return $this;
    }

    function getZone2Price()
    {
        return $this->zone2_price;
    }

    function setZone2Price($value = '0')
    {
        $this->zone2_price = $value;
        return $this;
    }

    function getZone3Price()
    {
        return $this->zone3_price;
    }

    function setZone3Price($value = '0')
    {
        $this->zone3_price = $value;
        return $this;
    }

    function getZone4Price()
    {
        return $this->zone4_price;
    }

    function setZone4Price($value = '0')
    {
        $this->zone4_price = $value;
        return $this;
    }

    function getZone5Price()
    {
        return $this->zone5_price;
    }

    function setZone5Price($value = '0')
    {
        $this->zone5_price = $value;
        return $this;
    }

    function getMinCod()
    {
        return $this->min_cod;
    }

    function setMinCod($value = '0')
    {
        $this->min_cod = $value;
        return $this;
    }

    function getCodPercent()
    {
        return $this->cod_percent;
    }

    function setCodPercent($value = '0')
    {
        $this->cod_percent = $value;
        return $this;
    }

    function getZonePrice($zone = '1')
    {
        switch ($zone) {
            case 'z1':
            case '1':
                return round($this->getZone1Price(), 2);
                break;
            case 'z2':
            case '2':
                return round($this->getZone2Price(), 2);
                break;
            case 'z3':
            case '3':
                return round($this->getZone3Price(), 2);
                break;
            case 'z4':
            case '4':
                return round($this->getZone4Price(), 2);
                break;
            case 'z5':
            case '5':
                return round($this->getZone5Price(), 2);
                break;
            default:
                return '0';
        }
    }
}