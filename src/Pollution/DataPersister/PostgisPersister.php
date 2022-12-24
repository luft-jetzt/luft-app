<?php declare(strict_types=1);

namespace App\Pollution\DataPersister;

use App\Pollution\StationCache\StationCacheInterface;
use App\Pollution\Value\Value;
use App\Pollution\ValueDataConverter\ValueDataConverter;
use Doctrine\Persistence\ManagerRegistry;

class PostgisPersister extends AbstractPersister
{
    public function __construct(protected ManagerRegistry $managerRegistry, protected StationCacheInterface $stationCache)
    {

    }

    public function persistValues(array $values): PersisterInterface
    {
        $em = $this->managerRegistry->getManager();

        /** @var Value $value */
        foreach ($values as $value) {
            if ($this->stationExists($value->getStation())) {
                $station = $this->getStationByCode($value->getStation());

                $data = ValueDataConverter::convert($value, $station);

                $em->persist($data);
            }

            $em->flush();


        }

        return $this;
    }

    public function getNewValueList(): array
    {
        return [];
    }

    public function reset(): PersisterInterface
    {
        return $this;
    }
}