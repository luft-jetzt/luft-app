<?php declare(strict_types=1);

namespace App\Air\Measurement;

use Symfony\Component\Serializer\Attribute\Ignore;

interface MeasurementInterface
{
    public const MEASUREMENT_PM10 = 1;
    public const MEASUREMENT_PM25 = 6;
    public const MEASUREMENT_O3 = 2;
    public const MEASUREMENT_NO2 = 3;
    public const MEASUREMENT_SO2 = 4;
    public const MEASUREMENT_CO = 5;
    public const MEASUREMENT_CO2 = 7;
    public const MEASUREMENT_UVINDEX = 8;
    public const MEASUREMENT_TEMPERATURE = 9;
    public const MEASUREMENT_CORONAINCIDENCE = 10;

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
