<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Parser;

use App\Entity\Station;
use App\Pollution\Value\Value;
use App\Provider\UmweltbundesamtDe\UmweltbundesamtDeProvider;
use Doctrine\Persistence\ManagerRegistry;

class Parser implements ParserInterface
{
    /** @var array $stationList */
    protected $stationList;

    protected ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function parse(array $response, int $pollutant): array
    {
        $this->fetchStationList();

        $valueList = [];

        foreach ($response['data'] as $stationId => $dataSet) {
            $data = array_pop($dataSet);

            if ($data[2] <= 0) {
                continue;
            }
            
            if (!array_key_exists($stationId, $this->stationList)) {
                continue;
            }

            $stationCode = $this->stationList[$stationId]->getStationCode();

            $dataValue = new Value();

            $dataValue
                ->setStation($stationCode)
                ->setDateTime(new \DateTimeImmutable($data[3]))
                ->setPollutant($pollutant)
                ->setValue($data[2]);

            $valueList[] = $dataValue;
        }

        return $valueList;
    }

    protected function fetchStationList(): Parser
    {
        $this->stationList = $this->registry->getRepository(Station::class)->findIndexedByProvider(UmweltbundesamtDeProvider::IDENTIFIER, 'ubaStationId');

        return $this;
    }
}
