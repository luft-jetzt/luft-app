<?php declare(strict_types=1);

namespace App\Provider\HqcasanovaProvider\SourceFetcher;

use App\Air\Measurement\CO2;
use App\Pollution\Value\Value;
use App\Producer\Value\ValueProducerInterface;
use App\Provider\HqcasanovaProvider\StationLoader\HqcasanovaStationLoader;
use App\SourceFetcher\FetchProcess;
use App\SourceFetcher\FetchResult;
use App\SourceFetcher\SourceFetcherInterface;
use Curl\Curl;

class SourceFetcher implements SourceFetcherInterface
{
    protected Curl $curl;

    protected ValueProducerInterface $valueProducer;

    public function __construct(HqcasanovaStationLoader $stationLoader, ValueProducerInterface $valueProducer)
    {
        $this->stationLoader = $stationLoader;
        $this->valueProducer = $valueProducer;

        $this->curl = new Curl();
    }

    public function fetch(FetchProcess $fetchProcess): FetchResult
    {
        $xmlFile = file_get_contents('https://www.esrl.noaa.gov/gmd/webdata/ccgg/trends/rss.xml');

        $simpleXml = new \SimpleXMLElement($xmlFile);

        $resultList = [];

        foreach ($simpleXml->channel->item as $item) {
            $guid = (string) $item->guid;

            if (!$guid || 1 !== preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $guid)) {
                continue;
            }

            preg_match('/\d{3,3}\.\d{1,2}/', (string) $item->description, $matches);

            $co2Value = array_pop($matches);

            $resultList[$guid] = $co2Value;
        }

        uksort($resultList, 'strnatcmp');

        $lastValueDateTimeString = array_key_last($resultList);
        $lastCo2Value = (float) $resultList[$lastValueDateTimeString];

        $value = new Value();
        $value->setValue($lastCo2Value)
            ->setStation('USHIMALO')
            ->setPollutant(CO2::MEASUREMENT_CO2)
            ->setDateTime(new \DateTimeImmutable,($lastValueDateTimeString));

        $this->valueProducer->publish($value);

        $fetchResult = new FetchResult();
        $fetchResult->setCounter('co2', 1);

        return $fetchResult;
    }
}
