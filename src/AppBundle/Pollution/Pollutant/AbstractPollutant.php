<?php

namespace AppBundle\Pollution\Pollutant;

use AppBundle\Pollution\PollutionLevel\PollutionLevel;

abstract class AbstractPollutant implements PollutantInterface
{
    /**
     * @var string $unitHtml
     */
    protected $unitHtml;

    /**
     * @var string $unitHtml
     */
    protected $unitPlain;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var PollutionLevel $pollutionLevel
     */
    protected $pollutionLevel;

    public function getUnitHtml(): string
    {
        return $this->unitHtml;
    }

    public function getUnitPlain(): string
    {
        return $this->unitPlain;
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
