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

    public function fetch(FetchProcess $fetchProcess): void
    {
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
                $this->$methodName($endDateTime, $startDateTime);
            }
        }
    }

    protected function fetchPM10(\DateTimeInterface $endDateTime, \DateTimeInterface $startDateTime = null)
    {
        $query = new UbaPM10Query();

        $this->fetchMeasurement($query, MeasurementInterface::MEASUREMENT_PM10);
    }

    protected function fetchSO2(\DateTimeInterface $endDateTime, \DateTimeInterface $startDateTime = null)
    {
        $query = new UbaSO2Query();

        $this->fetchMeasurement($query, MeasurementInterface::MEASUREMENT_SO2);
    }

    protected function fetchNO2(\DateTimeInterface $endDateTime, \DateTimeInterface $startDateTime = null)
    {
        $query = new UbaNO2Query();

        $this->fetchMeasurement($query, MeasurementInterface::MEASUREMENT_NO2);
    }

    protected function fetchO3(\DateTimeInterface $endDateTime, \DateTimeInterface $startDateTime = null)
    {
        $query = new UbaO3Query();

        $this->fetchMeasurement($query, MeasurementInterface::MEASUREMENT_O3);
    }

    protected function fetchCO(\DateTimeInterface $endDateTime, \DateTimeInterface $startDateTime = null)
    {
        $query = new UbaCOQuery();

        $this->fetchMeasurement($query, MeasurementInterface::MEASUREMENT_CO);
    }

    protected function fetchMeasurement(UbaQueryInterface $query, int $pollutant)
    {
        $response = $this->query($query);

        $valueList = $this->parser->parse($response, $pollutant);

        foreach ($valueList as $value) {
            $this->valueProducer->publish($value);
        }
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
