<?php declare(strict_types=1);

namespace App\Pollution\DataPersister;

use App\Pollution\StationCache\StationCacheInterface;
use App\Pollution\UniqueStrategy\UniqueStrategyInterface;
use App\Pollution\Value\Value;
use App\Pollution\ValueDataConverter\ValueDataConverter;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

class OrmPersister extends AbstractPersister
{
    protected ManagerRegistry $doctrine;
    protected ObjectManager $entityManager;
    protected array $stationList = [];

    protected array $newValueList = [];

    protected UniqueStrategyInterface $uniqueStrategy;

    public function __construct(ManagerRegistry $doctrine, StationCacheInterface $stationCache, UniqueStrategyInterface $uniqueStrategy)
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $doctrine->getManager();
        $this->uniqueStrategy = $uniqueStrategy;

        parent::__construct($stationCache);
    }

    public function persistValues(array $values): PersisterInterface
    {
        if (0 === count($values)) {
            return $this;
        }

        $this->uniqueStrategy->init($values);

        /** @var Value $value */
        foreach ($values as $value) {
            if ($this->stationExists($value->getStation())) {
                $station = $this->getStationByCode($value->getStation());

                $data = ValueDataConverter::convert($value, $station);

                if (!$data) {
                    continue;
                }
            } else {
                continue;
            }

            if ($this->uniqueStrategy->isDataDuplicate($data)) {
                continue;
            }

            $this->uniqueStrategy->addData($data);

            $this->entityManager->persist($data);

            $this->newValueList[] = $data;
        }

        $this->entityManager->flush();

        $this->uniqueStrategy->save();

        return $this;
    }

    public function reset(): PersisterInterface
    {
        $this->stationList = [];
        $this->newValueList = [];

        return $this;
    }

    public function getNewValueList(): array
    {
        return $this->newValueList;
    }
}
