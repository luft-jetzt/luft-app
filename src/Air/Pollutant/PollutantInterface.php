<?php declare(strict_types=1);

namespace App\Air\Pollutant;

use Symfony\Component\Serializer\Attribute\Ignore;

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

    public function getUnitHtml(): string;

    public function getUnitPlain(): string;

    public function getName(): string;

    public function getIdentifier(): string;

    public function getShortNameHtml(): string;

    #[Ignore()]
    public function showOnMap(): bool;

    #[Ignore()]
    public function includeInTweets(): bool;
}
