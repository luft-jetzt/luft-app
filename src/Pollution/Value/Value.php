<?php declare(strict_types=1);

namespace App\Pollution\Value;

use Symfony\Component\Serializer\Attribute\Ignore;

class Value
{
    protected ?string $stationCode = null;

    protected ?\DateTime $dateTime = null;

    protected ?float $value = null;

    protected ?string $pollutant = null;

    protected ?string $tag = null;

    public function __construct()
    {

    }

    public function getStation(): ?string
    {
        return $this->stationCode;
    }

    public function setStation(string $stationCode): Value
    {
        $this->stationCode = $stationCode;

        return $this;
    }

    public function getDateTime(): ?\DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): Value
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): Value
    {
        $this->value = $value;

        return $this;
    }

    public function getPollutant(): ?string
    {
        return $this->pollutant;
    }

    public function setPollutant(string $pollutant): Value
    {
        $this->pollutant = $pollutant;

        return $this;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): Value
    {
        $this->tag = $tag;

        return $this;
    }
}
