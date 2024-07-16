<?php declare(strict_types=1);

namespace App\Air\SourceFetcher;

use Caldera\GeoBasic\Coord\CoordInterface;

class FetchProcess
{
    protected array $pollutantList = [];

    protected ?\DateTimeInterface $fromDateTime = null;

    protected ?\DateTimeInterface $untilDateTime = null;

    protected ?CoordInterface $coord = null;

    protected ?\DateInterval $interval = null;

    public function getPollutantList(): array
    {
        return $this->pollutantList;
    }

    public function setPollutantList(array $pollutantList): FetchProcess
    {
        $this->pollutantList = $pollutantList;

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
