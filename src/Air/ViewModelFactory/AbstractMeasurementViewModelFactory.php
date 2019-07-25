<?php declare(strict_types=1);

namespace App\Air\ViewModelFactory;

use App\Air\AirQuality\Calculator\AirQualityCalculatorInterface;
use App\Air\Measurement\CO;
use App\Air\Measurement\CO2;
use App\Air\Measurement\MeasurementInterface;
use App\Air\Measurement\NO2;
use App\Air\Measurement\O3;
use App\Air\Measurement\PM10;
use App\Air\Measurement\PM25;
use App\Air\Measurement\SO2;
use App\Air\Measurement\Temperature;
use App\Air\Measurement\UVIndex;
use Caldera\GeoBasic\Coord\CoordInterface;

abstract class AbstractMeasurementViewModelFactory implements MeasurementViewModelFactoryInterface
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

    public function setPollutantList(array $pollutantList): MeasurementViewModelFactoryInterface
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
            case 7: return new CO2();
            case 8: return new UVIndex();
            case 9: return new Temperature();
        }
    }

    public function setCoord(CoordInterface $coord): MeasurementViewModelFactoryInterface
    {
        $this->coord = $coord;

        return $this;
    }

    abstract public function decorate(): MeasurementViewModelFactoryInterface;
}
