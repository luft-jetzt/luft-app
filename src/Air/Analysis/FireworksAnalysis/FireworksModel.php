<?php declare(strict_types=1);

namespace App\Air\Analysis\FireworksAnalysis;

use App\Entity\Data;
use App\Entity\Station;

class FireworksModel
{
    public function __construct(protected Station $station, protected Data $data, protected float $slope)
    {
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
