<?php

namespace AppBundle\Pollution\Pollutant;

use AppBundle\Pollution\PollutionLevel\PollutionLevel;
use JMS\Serializer\Annotation as JMS;

interface PollutantInterface
{
    const POLLUTANT_PM10 = 1;
    const POLLUTANT_O3 = 2;
    const POLLUTANT_NO2 = 3;
    const POLLUTANT_SO2 = 4;
    const POLLUTANT_CO = 5;

    /**
     * @JMS\Expose()
     */
    public function getUnit(): string;

    /**
     * @JMS\Expose()
     */
    public function getName(): string;

    /**
     * @JMS\Expose()
     */
    public function getPollutionLevel(): PollutionLevel;
}