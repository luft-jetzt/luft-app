<?php declare(strict_types=1);

namespace App\Pollution\Pollutant;

use App\Pollution\PollutionLevel\PollutionLevel;
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
    public function getUnitHtml(): string;

    /**
     * @JMS\Expose()
     */
    public function getUnitPlain(): string;

    /**
     * @JMS\Expose()
     */
    public function getName(): string;

    /**
     * @JMS\Expose()
     */
    public function getPollutionLevel(): PollutionLevel;
}
