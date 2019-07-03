<?php declare(strict_types=1);

namespace App\Measurement;

use JMS\Serializer\Annotation as JMS;

interface MeasurementInterface
{
    const MEASUREMENT_PM10 = 1;
    const MEASUREMENT_PM25 = 6;
    const MEASUREMENT_O3 = 2;
    const MEASUREMENT_NO2 = 3;
    const MEASUREMENT_SO2 = 4;
    const MEASUREMENT_CO = 5;

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
    public function getIdentifier(): string;

    /**
     * @JMS\Expose()
     */
    public function getShortNameHtml(): string;
}
