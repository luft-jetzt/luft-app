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
use Curl\Curl;

class SourceFetcher
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

    public function fetch(array $measurementList): void
    {
        //if ($input->getArgument('endDateTime')) {
        //    $endDateTime = new \DateTimeImmutable($input->getArgument('endDateTime'));
        //} else {
        $endDateTime = new \DateTimeImmutable();
        //}

        //if ($input->getArgument('startDateTime')) {
        //    $startDateTime = new \DateTimeImmutable($input->getArgument('startDateTime'));
        //} elseif ($input->getOption('interval')) {
        //    $startDateTime = $endDateTime->sub(new \DateInterval(sprintf('PT%dH', $input->getOption('interval'))));
        //} else {
        $startDateTime = $endDateTime->sub(new \DateInterval(sprintf('PT2H')));
        //}

        //$output->writeln(sprintf('Fetching uba pollution data from <info>%s</info> to <info>%s</info>', $startDateTime->format('Y-m-d H:i:s'), $endDateTime->format('Y-m-d H:i:s')));

        /** @var MeasurementInterface $measurement */
        foreach ($measurementList as $measurement) {
            $methodName = sprintf('fetch%s', strtoupper($measurement->getIdentifier()));

            $this->$methodName($endDateTime, $startDateTime);
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
