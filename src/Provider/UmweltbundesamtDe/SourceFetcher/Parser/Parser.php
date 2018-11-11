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

    public function parse(\stdClass $response, int $pollutant): array
    {
        $data = array_pop($response->data);

        $valueList = [];

        foreach ($data as $stationCode => $dataList) {
            $dateTime = $this->query->getReporting()->getStartDateTime();

            foreach ($dataList as $value) {
                $dataValue = new Value();

                $dataValue
                    ->setStation($stationCode)
                    ->setDateTime($dateTime)
                    ->setPollutant($pollutant)
                    ->setValue($value);

                $valueList[] = $dataValue;

                $dateTime->add($this->query->getReporting()->getDateInterval());
            }
        }

        return $valueList;
    }
}
