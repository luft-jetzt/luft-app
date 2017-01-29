<?php

namespace AppBundle\Pollution\Box;

use AppBundle\Entity\Data;
use AppBundle\Entity\Station;
use AppBundle\Pollution\Pollutant\PollutantInterface;

class Box
{
    protected $station;
    protected $data;
    protected $pollutant;
    protected $pollutionLevel;
    protected $caption;

    public function __construct(Data $data)
    {
        $this->data = $data;
    }

    public function getStation(): Station
    {
        return $this->station;
    }

    public function setStation(Station $station): Box
    {
        $this->station = $station;

        return $this;
    }

    public function getData(): Data
    {
        return $this->data;
    }

    public function setData($data): Box
    {
        $this->data = $data;

        return $this;
    }

    public function getPollutant(): int
    {
        return $this->pollutant;
    }

    public function setPollutant(PollutantInterface $pollutant): Box
    {
        $this->pollutant = $pollutant;

        return $this;
    }

    public function getPollutionLevel(): int
    {
        return $this->pollutionLevel;
    }

    public function setPollutionLevel(int $pollutionLevel): Box
    {
        $this->pollutionLevel = $pollutionLevel;

        return $this;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): Box
    {
        $this->caption = $caption;

        return $this;
    }
}
