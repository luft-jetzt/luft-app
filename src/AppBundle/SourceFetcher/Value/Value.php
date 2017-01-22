<?php

namespace AppBundle\SourceFetcher\Value;

class Value
{
    protected $station;
    protected $dateTime;
    protected $value;

    public function __construct()
    {

    }

    public function getStation(): string
    {
        return $this->station;
    }

    public function setStation(string $station): Value
    {
        $this->station = $station;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): Value
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): Value
    {
        $this->value = $value;

        return $this;
    }


}