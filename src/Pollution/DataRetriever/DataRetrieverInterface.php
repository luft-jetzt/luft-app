<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever;

use App\Entity\Data;
use App\Entity\Station;

interface DataRetrieverInterface
{
    public function retrieveStationData(Station $station, int $pollutant, \DateTime $dateTime = null): ?Data;
}
