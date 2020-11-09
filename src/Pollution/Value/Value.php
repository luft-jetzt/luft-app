<?php declare(strict_types=1);

namespace App\Pollution\Value;

use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("all")
 */
class Value
{
    /**
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected string $station;

    /**
     * @JMS\Expose
     * @JMS\Type("DateTimeImmutable<'U'>")
     */
    protected \DateTimeImmutable $dateTime;

    /**
     * @JMS\Expose
     * @JMS\Type("float")
     */
    protected float $value;

    /**
     * @JMS\Expose
     * @JMS\Type("int")
     */
    protected int $pollutant;

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

    public function getDateTime(): \DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeInterface $dateTime): Value
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
