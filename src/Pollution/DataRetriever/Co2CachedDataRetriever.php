<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever;

use App\Air\Measurement\MeasurementInterface;
use App\Entity\Station;
use App\Pollution\DataCache\DataCacheInterface;
use App\Pollution\DataCache\KeyGenerator;
use Caldera\GeoBasic\Coord\CoordInterface;
use Doctrine\Persistence\ManagerRegistry;

class Co2CachedDataRetriever implements DataRetrieverInterface
{
    public function __construct(protected DataCacheInterface $dataCache, protected ManagerRegistry $registry)
    {
    }

    public function retrieveDataForCoord(CoordInterface $coord, int $pollutantId = null, \DateTime $fromDateTime = null, \DateInterval $dateInterval = null, float $maxDistance = 20.0, int $maxResults = 750): array
    {
        if ($coord instanceof Station) { // TODO this should be regulated through a service
            return [];
        }
        
        if (MeasurementInterface::MEASUREMENT_CO2 !== $pollutantId) {
            return [];
        }

        $station = $this->registry->getRepository(Station::class)->findOneByStationCode('USHIMALO');

        $dataKey = KeyGenerator::generateKeyForStationAndPollutant($station, MeasurementInterface::MEASUREMENT_CO2);

        $data = $this->dataCache->getData($dataKey);

        if ($data) {
            return [$data];
        }

        return [];
    }
}
