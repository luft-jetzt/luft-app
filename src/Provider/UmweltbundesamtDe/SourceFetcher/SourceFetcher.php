<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher;

use App\Air\Measurement\MeasurementInterface;
use App\Producer\Value\ValueProducerInterface;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Parser\ParserInterface;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaCOQuery;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaNO2Query;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaO3Query;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaPM10Query;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaQueryInterface;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaSO2Query;
use App\Provider\UmweltbundesamtDe\SourceFetcher\QueryBuilder\QueryBuilder;
use App\SourceFetcher\FetchProcess;
use App\SourceFetcher\FetchResult;
use App\SourceFetcher\SourceFetcherInterface;
use Curl\Curl;

class SourceFetcher implements SourceFetcherInterface
{
    protected Curl $curl;

    protected ValueProducerInterface $valueProducer;

    protected ParserInterface $parser;

    public function __construct(ValueProducerInterface $valueProducer, ParserInterface $parser)
    {
        $this->curl = new Curl();
        $this->valueProducer = $valueProducer;
        $this->parser = $parser;
    }

    public function fetch(FetchProcess $fetchProcess): FetchResult
    {
        $fetchResult = new FetchResult();

        if ($fetchProcess->getUntilDateTime()) {
            $endDateTime = $fetchProcess->getUntilDateTime();
        } else {
            $endDateTime = new \DateTimeImmutable();
        }

        if ($fetchProcess->getFromDateTime()) {
            $startDateTime = $fetchProcess->getFromDateTime();
        } elseif ($fetchProcess->getInterval()) {
            //$startDateTime = $endDateTime->sub(new \DateInterval(sprintf('PT%dH', $input->getOption('interval'))));
            $startDateTime = $endDateTime->sub($fetchProcess->getInterval());
        } else {
            $startDateTime = $endDateTime->sub(new \DateInterval(sprintf('PT2H')));
        }

        /** @var MeasurementInterface $measurement */
        foreach ($fetchProcess->getMeasurementList() as $measurement) {
            $methodName = sprintf('fetch%s', strtoupper($measurement->getIdentifier()));

            if (method_exists($this, $methodName)) {
                 $this->$methodName($fetchResult, $endDateTime, $startDateTime);
            }
        }

        return $fetchResult;
    }

    protected function fetchPM10(FetchResult $fetchResult, \DateTimeInterface $endDateTime, \DateTimeInterface $startDateTime = null): void
    {
        $query = new UbaPM10Query();

        $this->fetchMeasurement($fetchResult, $query, MeasurementInterface::MEASUREMENT_PM10);
    }

    protected function fetchSO2(FetchResult $fetchResult, \DateTimeInterface $endDateTime, \DateTimeInterface $startDateTime = null): void
    {
        $query = new UbaSO2Query();

        $this->fetchMeasurement($fetchResult, $query, MeasurementInterface::MEASUREMENT_SO2);
    }

    protected function fetchNO2(FetchResult $fetchResult, \DateTimeInterface $endDateTime, \DateTimeInterface $startDateTime = null): void
    {
        $query = new UbaNO2Query();

        $this->fetchMeasurement($fetchResult, $query, MeasurementInterface::MEASUREMENT_NO2);
    }

    protected function fetchO3(FetchResult $fetchResult, \DateTimeInterface $endDateTime, \DateTimeInterface $startDateTime = null): void
    {
        $query = new UbaO3Query();

        $this->fetchMeasurement($fetchResult, $query, MeasurementInterface::MEASUREMENT_O3);
    }

    protected function fetchCO(FetchResult $fetchResult, \DateTimeInterface $endDateTime, \DateTimeInterface $startDateTime = null): void
    {
        $query = new UbaCOQuery();

        $this->fetchMeasurement($fetchResult, $query, MeasurementInterface::MEASUREMENT_CO);
    }

    protected function fetchMeasurement(FetchResult $fetchResult, UbaQueryInterface $query, int $pollutant): void
    {
        $response = $this->query($query);

        $valueList = $this->parser->parse($response, $pollutant);

        foreach ($valueList as $value) {
            $this->valueProducer->publish($value);
        }

        $fetchResult->incCounter((string) $pollutant, count($valueList));
    }

    protected function query(UbaQueryInterface $query): array
    {
        $data = QueryBuilder::buildQueryString($query);

        $queryString = sprintf('https://www.umweltbundesamt.de/api/air_data/v2/measures/json?%s', $data);

        $this->curl->get($queryString);

        $response = $this->curl->rawResponse;

        return json_decode($response, true);
    }
}
