<?php

namespace AppBundle\Pollution\Pollutant;

use AppBundle\Pollution\PollutionLevel\PollutionLevel;

abstract class AbstractPollutant implements PollutantInterface
{
    /**
     * @var string $unit
     */
    protected $unit;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var PollutionLevel $pollutionLevel
     */
    protected $pollutionLevel;

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPollutionLevel(): PollutionLevel
    {
        return $this->pollutionLevel;
    }
}
