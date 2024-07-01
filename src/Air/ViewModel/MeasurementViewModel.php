<?php declare(strict_types=1);

namespace App\Air\ViewModel;

use App\Air\Measurement\MeasurementInterface;
use App\Entity\Data;
use App\Entity\Station;

class MeasurementViewModel
{
    protected ? Station $station = null;

    protected ?MeasurementInterface $measurement = null;

    protected ?int $pollutionLevel = null;

    protected ?string $caption = null;

    protected ?float $distance = null;

    public function __construct(
        protected ?Data $data
    )
    {
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
