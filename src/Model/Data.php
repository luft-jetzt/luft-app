<?php declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("ALL")
 */
class Data
{
    /**
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    protected ?string $stationCode = null;

    /**
     * @JMS\Expose()
     * @JMS\Type("DateTime<'U'>")
     */
    protected ?\DateTime $dateTime = null;

    /**
     * @JMS\Expose()
     * @JMS\Type("float")
     */
    protected ?float $value = null;

    /**
     * @JMS\Expose()
     * @JMS\Type("integer")
     */
    protected ?int $pollutant = null;

    public function getStationCode(): string
    {
        return $this->stationCode;
    }

    public function setStationCode(string $stationCode): Data
    {
        $this->stationCode = $stationCode;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): Data
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): Data
    {
        $this->value = $value;

        return $this;
    }

    public function getPollutant(): int
    {
        return $this->pollutant;
    }

    public function setPollutant(int $pollutant): Data
    {
        $this->pollutant = $pollutant;

        return $this;
    }
}
