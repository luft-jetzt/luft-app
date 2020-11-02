<?php declare(strict_types=1);

namespace App\Provider\NoaaProvider\SourceFetcher;

use App\Air\Measurement\CO2;
use App\Pollution\Value\Value;
use App\Producer\Value\ValueProducerInterface;
use App\SourceFetcher\FetchProcess;
use App\SourceFetcher\FetchResult;
use App\SourceFetcher\SourceFetcherInterface;
use Curl\Curl;

class SourceFetcher implements SourceFetcherInterface
{
    protected ValueProducerInterface $valueProducer;

    public function __construct(ValueProducerInterface $valueProducer)
    {
        $this->valueProducer = $valueProducer;

        $this->curl = new Curl();
    }

    public function fetch(FetchProcess $fetchProcess): FetchResult
    {
        $xmlFile = file_get_contents('https://www.esrl.noaa.gov/gmd/webdata/ccgg/trends/rss.xml');

        $simpleXml = new \SimpleXMLElement($xmlFile);

        $resultList = $this->parseXmlFile($simpleXml);

        $lastValueDateTimeString = array_key_last($resultList);
        $lastCo2Value = (float) $resultList[$lastValueDateTimeString];

        $value = $this->createValue($lastCo2Value, new \DateTimeImmutable($lastValueDateTimeString));

        $this->valueProducer->publish($value);

        $fetchResult = new FetchResult();
        $fetchResult->setCounter('co2', 1);

        return $fetchResult;
    }

    protected function createValue(float $lastCo2Value, \DateTimeImmutable $dateTime): Value
    {
        $value = new Value();
        $value->setValue($lastCo2Value)
            ->setStation('USHIMALO')
            ->setPollutant(CO2::MEASUREMENT_CO2)
            ->setDateTime($dateTime);

        return $value;
    }

    protected function parseXmlFile(\SimpleXMLElement $xmlRoot): array
    {
        $resultList = [];

        foreach ($xmlRoot->channel->item as $item) {
            $guid = (string) $item->guid;

            if (!$guid || !$this->isYearMonthDayGuidString($guid)) {
                continue;
            }

            $co2Value = $this->fetchCo2ValueFromString((string) $item->description);

            $resultList[$guid] = $co2Value;
        }

        uksort($resultList, 'strnatcmp');

        return $resultList;
    }

    protected function isYearMonthDayGuidString(string $guid): bool
    {
        return 1 === preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $guid);
    }

    protected function fetchCo2ValueFromString(string $description): float
    {
        preg_match('/\d{3,3}\.\d{1,2}/', $description, $matches);

        return (float) array_pop($matches);
    }
}
