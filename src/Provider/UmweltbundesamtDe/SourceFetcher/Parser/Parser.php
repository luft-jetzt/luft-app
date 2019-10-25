<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Parser;

use App\Pollution\Value\Value;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaQueryInterface;

class Parser implements ParserInterface
{
    /** @var UbaQueryInterface $query */
    protected $query = null;

    public function __construct(UbaQueryInterface $query)
    {
        $this->query = $query;
    }

    public function parse(array $response, int $pollutant): array
    {
        foreach ($response['data'] as $stationId => $dataSet) {
            $data = array_pop($dataSet);

            $dataValue = new Value();

            $dataValue
                //->setStation($stationCode) // !!!
                ->setDateTime(new \DateTime($data[3]))
                ->setPollutant($pollutant)
                ->setValue($data[2]);

            $valueList[] = $dataValue;
        }

        dump($valueList);die;
        return $valueList;
    }
}
