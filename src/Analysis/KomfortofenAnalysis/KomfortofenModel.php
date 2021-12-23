<?php declare(strict_types=1);

namespace App\Analysis\KomfortofenAnalysis;

use App\Entity\Data;
use App\Entity\Station;

class KomfortofenModel
{
    protected Station $station;
    protected Data $data;
    protected float $slope;

    public function __construct(Station $station, Data $data, float $slope)
    {
        $this->data = $data;
        $this->station = $station;
        $this->slope = $slope;
    }

    public function getStation(): Station
    {
        return $this->station;
    }

    public function getData(): Data
    {
        return $this->data;
    }

    public function getSlope(): float
    {
        return $this->slope;
    }
}
