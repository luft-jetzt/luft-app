<?php declare(strict_types=1);

namespace App\Provider\Luftdaten\SourceFetcher\Value;

class Value
{
    /** @var string $station */
    protected $station;

    /** @var \DateTime $dateTime */
    protected $dateTime;

    /** @var float $value */
    protected $value;

    /** @var int $pollutant */
    protected $pollutant;

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

    public function getPollutant(): int
    {
        return $this->pollutant;
    }

    public function setPollutant(int $pollutant): Value
    {
        $this->pollutant = $pollutant;

        return $this;
    }

}
