<?php

namespace AppBundle\Pollution\BoxDecorator;

use AppBundle\Pollution\Pollutant\CO;
use AppBundle\Pollution\Pollutant\NO2;
use AppBundle\Pollution\Pollutant\O3;
use AppBundle\Pollution\Pollutant\PM10;
use AppBundle\Pollution\Pollutant\PollutantInterface;
use AppBundle\Pollution\Pollutant\SO2;

abstract class AbstractBoxDecorator implements BoxDecoratorInterface
{
    protected $boxList = [];

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
            case 2: return new O3();
            case 3: return new NO2();
            case 4: return new SO2();
            case 5: return new CO();
        }
    }

    abstract public function decorate(): BoxDecoratorInterface;
}
