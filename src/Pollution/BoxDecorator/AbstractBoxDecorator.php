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

abstract class AbstractBoxDecorator implements BoxDecoratorInterface
{
    /** @var array $boxList */
    protected $boxList = [];

    /** @var AirQualityCalculatorInterface $airQualityCalculator */
    protected $airQualityCalculator;

    public function __construct(AirQualityCalculatorInterface $airQualityCalculator)
    {
        $this->airQualityCalculator = $airQualityCalculator;
    }

    public function setBoxList(array $boxList): BoxDecoratorInterface
    {
        $this->boxList = $boxList;

        return $this;
    }

    public function getBoxList(): array
    {
        return $this->boxList;
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

    abstract public function decorate(): BoxDecoratorInterface;
}
