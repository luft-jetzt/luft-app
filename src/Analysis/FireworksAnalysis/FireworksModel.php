<?php declare(strict_types=1);

namespace App\Analysis\FireworksAnalylsis;

use App\Entity\Data;
use App\Entity\Station;

class FireworksModel
{
    /** @var Station $station */
    protected $station;

    /** @var Data $data */
    protected $data;

    /** @var float $slope */
    protected $slope;

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
