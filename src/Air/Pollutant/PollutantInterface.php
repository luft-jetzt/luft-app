<?php declare(strict_types=1);

namespace App\Air\Pollutant;

use JMS\Serializer\Annotation as JMS;

interface PollutantInterface
{
    public const POLLUTANT_PM10 = 1;
    public const POLLUTANT_PM25 = 6;
    public const POLLUTANT_O3 = 2;
    public const POLLUTANT_NO2 = 3;
    public const POLLUTANT_SO2 = 4;
    public const POLLUTANT_CO = 5;
    public const POLLUTANT_CO2 = 7;
    public const POLLUTANT_UVINDEX = 8;
    public const POLLUTANT_TEMPERATURE = 9;
    public const POLLUTANT_UVINDEXMAX = 11;

    #[JMS\Expose]
    public function getUnitHtml(): string;

    #[JMS\Expose]
    public function getUnitPlain(): string;

    #[JMS\Expose]
    public function getName(): string;

    #[JMS\Expose]
    public function getIdentifier(): string;

    #[JMS\Expose]
    public function getShortNameHtml(): string;

    public function showOnMap(): bool;

    public function includeInTweets(): bool;
}
