<?php declare(strict_types=1);

namespace App\Air\DataPersister;

use App\Air\StationCache\StationCacheInterface;
use App\Air\ValueDataConverter\ValueDataConverter;
use Caldera\LuftModel\Model\Value;
use Doctrine\Persistence\ManagerRegistry;

class PostgisPersister extends AbstractPersister
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        StationCacheInterface $stationCache
    )
    {
        parent::__construct($stationCache);
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
