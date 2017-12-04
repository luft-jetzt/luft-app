<?php

namespace AppBundle\Pollution\DataRetriever;

use AppBundle\Entity\Data;
use AppBundle\Entity\Station;

interface DataRetrieverInterface
{
    public function retrieveStationData(Station $station, string $pollutant): ?Data;
}
