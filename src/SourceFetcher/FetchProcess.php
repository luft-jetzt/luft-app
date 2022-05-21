<?php declare(strict_types=1);

namespace App\SourceFetcher;

use Caldera\GeoBasic\Coord\CoordInterface;

class FetchProcess
{
    protected array $measurementList = [];

    protected ?\DateTimeInterface $fromDateTime = null;

    protected ?\DateTimeInterface $untilDateTime = null;

    protected ?CoordInterface $coord = null;

    protected ?\DateInterval $interval = null;

    public function getMeasurementList(): array
    {
        return $this->measurementList;
    }

    public function setMeasurementList(array $measurementList): FetchProcess
    {
        $this->measurementList = $measurementList;

        return $this;
    }

    public function getFromDateTime(): ?\DateTimeInterface
    {
        return $this->fromDateTime;
    }

    public function setFromDateTime(?\DateTimeInterface $fromDateTime): FetchProcess
    {
        $this->fromDateTime = $fromDateTime;

        return $this;
    }

    public function getUntilDateTime(): ?\DateTimeInterface
    {
        return $this->untilDateTime;
    }

    public function setUntilDateTime(?\DateTimeInterface $untilDateTime): FetchProcess
    {
        $this->untilDateTime = $untilDateTime;

        return $this;
    }

    public function getCoord(): ?CoordInterface
    {
        return $this->coord;
    }

    public function setCoord(?CoordInterface $coord): FetchProcess
    {
        $this->coord = $coord;

        return $this;
    }

    public function getInterval(): ?\DateInterval
    {
        return $this->interval;
    }

    public function setInterval(?\DateInterval $interval): FetchProcess
    {
        $this->interval = $interval;

        return $this;
    }
}
