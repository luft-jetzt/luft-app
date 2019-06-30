<?php declare(strict_types=1);

namespace App\Pollution\BoxDecorator;

use App\AirQuality\Calculator\AirQualityCalculatorInterface;
use App\Pollution\Pollutant\CO;
use App\Pollution\Pollutant\NO2;
use App\Pollution\Pollutant\O3;
use App\Pollution\Pollutant\PM10;
use App\Pollution\Pollutant\PM25;
use App\Pollution\Pollutant\PollutantInterface;
use App\Pollution\Pollutant\SO2;
use Caldera\GeoBasic\Coord\CoordInterface;

/**
 * @deprecated
 */
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

    protected function getPollutantById(int $pollutantId): PollutantInterface
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
