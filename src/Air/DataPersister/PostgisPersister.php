<?php declare(strict_types=1);

namespace App\Air\DataPersister;

use App\Air\StationCache\StationCacheInterface;
use App\Air\Value\Value;
use App\Air\ValueDataConverter\ValueDataConverter;
use Doctrine\Persistence\ManagerRegistry;

class PostgisPersister extends AbstractPersister
{
    public function __construct(protected ManagerRegistry $managerRegistry, protected StationCacheInterface $stationCache)
    {

    }

    #[\Override]
    public function persistValues(array $values): PersisterInterface
    {
        $em = $this->managerRegistry->getManager();

        /** @var Value $value */
        foreach ($values as $value) {
            if ($this->stationExists($value->getStationCode())) {
                $station = $this->getStationByCode($value->getStationCode());

                $data = ValueDataConverter::convert($value, $station);

                $em->persist($data);
            }
        }

        $em->flush();

        return $this;
    }

    #[\Override]
    public function getNewValueList(): array
    {
        return [];
    }

    #[\Override]
    public function reset(): PersisterInterface
    {
        return $this;
    }
}
