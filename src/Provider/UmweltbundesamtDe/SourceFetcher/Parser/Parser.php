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

        $timeScope = array_pop($response->time_scope);
        $interval = new \DateInterval(sprintf('PT%dS', $timeScope));

        $valueList = [];

        foreach ($data as $stationCode => $dataList) {
            $dateTime = $this->query->getReporting()->getStartDateTime();

            foreach ($dataList as $value) {
                $value = $this->query->getFilter()->filter($value);

                if (!$value) {
                    $dateTime = $dateTime->add($interval);

                    continue;
                }

                $dataValue = new Value();

                $dataValue
                    ->setStation($stationCode)
                    ->setDateTime($dateTime)
                    ->setPollutant($pollutant)
                    ->setValue($value);

                $valueList[] = $dataValue;

                $dateTime = $dateTime->add($interval);
            }
        }

        return $valueList;
    }
}
