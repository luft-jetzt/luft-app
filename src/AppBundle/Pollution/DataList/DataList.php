<?php

namespace AppBundle\Pollution\DataList;

use AppBundle\Pollution\Box\Box;
use AppBundle\Pollution\Pollutant\PollutantInterface;

class DataList
{
    protected $boxes;

    public function __construct()
    {
        $this->boxes = [
            PollutantInterface::POLLUTANT_PM10 => null,
            PollutantInterface::POLLUTANT_O3 => null,
            PollutantInterface::POLLUTANT_NO2 => null,
            PollutantInterface::POLLUTANT_SO2 => null,
            PollutantInterface::POLLUTANT_CO => null,
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
