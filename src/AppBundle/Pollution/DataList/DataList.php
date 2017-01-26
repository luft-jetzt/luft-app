<?php

namespace AppBundle\Pollution\DataList;

use AppBundle\Pollution\Box\Box;
use AppBundle\Pollution\Pollutant\Pollutant;

class DataList
{
    protected $boxes;

    public function __construct()
    {
        $this->boxes = [
            Pollutant::POLLUTANT_PM10 => null,
            Pollutant::POLLUTANT_O3 => null,
            Pollutant::POLLUTANT_NO2 => null,
            Pollutant::POLLUTANT_SO2 => null,
            Pollutant::POLLUTANT_CO => null,
        ];
    }

    public function addBox(Box $box): DataList
    {
        $this->boxes[$box->getPollutant()] = $box;

        return $this;
    }

    public function getBoxes(): array
    {
        return $this->boxes;
    }

}
