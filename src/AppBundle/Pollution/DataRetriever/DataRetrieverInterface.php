<?php declare(strict_types=1);

namespace AppBundle\Pollution\DataRetriever;

use AppBundle\Entity\Data;
use AppBundle\Entity\Station;

interface DataRetrieverInterface
{
    public function retrieveStationData(Station $station, int $pollutant): ?Data;
}
