<?php declare(strict_types=1);

namespace App\Air\ViewModelFactory;

use App\Air\AirQuality\Calculator\AirQualityCalculatorInterface;
use App\Air\Pollutant\CO;
use App\Air\Pollutant\CO2;
use App\Air\Pollutant\PollutantInterface;
use App\Air\Pollutant\NO2;
use App\Air\Pollutant\O3;
use App\Air\Pollutant\PM10;
use App\Air\Pollutant\PM25;
use App\Air\Pollutant\SO2;
use App\Air\Pollutant\Temperature;
use App\Air\Pollutant\UVIndex;
use App\Air\Pollutant\UVIndexMax;
use Caldera\GeoBasic\Coord\CoordInterface;
use Caldera\GeoBasic\Coordinate\CoordinateInterface;

abstract class AbstractMeasurementViewModelFactory implements MeasurementViewModelFactoryInterface
{
    protected array $pollutantList = [];

    protected CoordInterface $coord;

    public function __construct(protected AirQualityCalculatorInterface $airQualityCalculator)
    {
    }

    #[\Override]
    public function setPollutantList(array $pollutantList): MeasurementViewModelFactoryInterface
    {
        $this->pollutantList = $pollutantList;

        return $this;
    }

    #[\Override]
    public function getPollutantList(): array
    {
        return $this->pollutantList;
    }

    protected function getPollutantById(int $pollutantId): PollutantInterface
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
            case 11: return new UVIndexMax();
        }
    }

    #[\Override]
    public function setCoord(CoordinateInterface $coord): MeasurementViewModelFactoryInterface
    {
        $this->coord = $coord;

        return $this;
    }

    #[\Override]
    abstract public function decorate(): MeasurementViewModelFactoryInterface;
}
