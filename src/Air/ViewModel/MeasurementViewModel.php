<?php declare(strict_types=1);

namespace App\Air\ViewModel;

use App\Air\Measurement\MeasurementInterface;
use App\Entity\Data;
use App\Entity\Station;
use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("ALL")
 */
class MeasurementViewModel
{
    /**
     * @var Station $station
     * @JMS\Expose()
     */
    protected $station;

    /**
     * @var Data $data
     * @JMS\Expose()
     */
    protected $data;

    /**
     * @var MeasurementInterface $measurement
     * @JMS\Expose()
     */
    protected $measurement;

    /**
     * @var int $pollutionLevel
     * @JMS\Expose()
     */
    protected $pollutionLevel;

    /**
     * @var string $caption
     * @JMS\Expose()
     */
    protected $caption;

    /**
     * @var float $distance
     * @JMS\Expose()
     */
    protected $distance;

    public function __construct(Data $data)
    {
        $this->data = $data;
    }

    public function getStation(): Station
    {
        return $this->station;
    }

    public function setStation(Station $station): self
    {
        $this->station = $station;

        return $this;
    }

    public function getData(): Data
    {
        return $this->data;
    }

    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getMeasurement(): MeasurementInterface
    {
        return $this->measurement;
    }

    public function setMeasurement(MeasurementInterface $measurement): self
    {
        $this->measurement = $measurement;

        return $this;
    }

    public function getPollutionLevel(): int
    {
        return $this->pollutionLevel;
    }

    public function setPollutionLevel(int $pollutionLevel): self
    {
        $this->pollutionLevel = $pollutionLevel;

        return $this;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function getDistance(): float
    {
        return $this->distance;
    }

    public function setDistance(float $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function showOnMap(): bool
    {
        return $this->measurement->showOnMap();
    }
}
