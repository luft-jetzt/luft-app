<?php declare(strict_types=1);

namespace App\Pollution\BoxDecorator;

use App\Air\Measurement\CO;
use App\Air\Measurement\MeasurementInterface;
use App\Air\Measurement\NO2;
use App\Air\Measurement\O3;
use App\Air\Measurement\PM10;
use App\Air\Measurement\PM25;
use App\Air\Measurement\SO2;
use App\AirQuality\Calculator\AirQualityCalculatorInterface;
use Caldera\GeoBasic\Coord\CoordInterface;

abstract class AbstractBoxDecorator implements BoxDecoratorInterface
{
    /** @var array $pollutantList */
    protected $pollutantList = [];

    /** @var AirQualityCalculatorInterface $airQualityCalculator */
    protected $airQualityCalculator;

    /** @var CoordInterface $coord */
    protected $coord;

    public function __construct(AirQualityCalculatorInterface $airQualityCalculator)
    {
        $this->airQualityCalculator = $airQualityCalculator;
    }

    public function setPollutantList(array $pollutantList): BoxDecoratorInterface
    {
        $this->pollutantList = $pollutantList;

        return $this;
    }

    public function getPollutantList(): array
    {
        return $this->pollutantList;
    }

    protected function getPollutantById(int $pollutantId): MeasurementInterface
    {
        switch ($pollutantId) {
            case 1: return new PM10();
            case 6: return new PM25();
            case 2: return new O3();
            case 3: return new NO2();
            case 4: return new SO2();
            case 5: return new CO();
        }
    }

    public function setCoord(CoordInterface $coord): BoxDecoratorInterface
    {
        $this->coord = $coord;

        return $this;
    }

    abstract public function decorate(): BoxDecoratorInterface;
}
