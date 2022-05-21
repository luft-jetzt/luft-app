<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever;

use App\Air\Measurement\MeasurementInterface;
use App\Entity\Data;
use App\Entity\Station;
use App\Pollution\ValueDataConverter\ValueDataConverter;
use App\Provider\OpenWeatherMapProvider\SourceFetcher\Parser\JsonParserInterface as OwmJsonParserInterface;
use App\Provider\OpenWeatherMapProvider\SourceFetcher\SourceFetcher as OwmSourceFetcher;
use App\Provider\CoronaProvider\SourceFetcher\Parser\JsonParserInterface as CoronaParserInterface;
use App\Provider\CoronaProvider\SourceFetcher\SourceFetcher as CoronaSourceFetcher;
use Caldera\GeoBasic\Coord\CoordInterface;

class AdhocDataRetriever implements DataRetrieverInterface
{
    protected OwmSourceFetcher $owmSourceFetcher;
    protected OwmJsonParserInterface $owmJsonParser;
    protected CoronaSourceFetcher $coronaSourceFetcher;
    protected CoronaParserInterface $coronaJsonParser;

    public function __construct(OwmSourceFetcher $owmSourceFetcher, OwmJsonParserInterface $owmJsonParser, CoronaParserInterface $coronaParserInterface, CoronaSourceFetcher $coronaSourceFetcher)
    {
        $this->owmSourceFetcher = $owmSourceFetcher;
        $this->owmJsonParser = $owmJsonParser;
        $this->coronaSourceFetcher = $coronaSourceFetcher;
        $this->coronaJsonParser = $coronaParserInterface;
    }

    public function retrieveDataForCoord(CoordInterface $coord, int $pollutantId = null, \DateTime $fromDateTime = null, \DateInterval $dateInterval = null, float $maxDistance = 20.0, int $maxResults = 250): array
    {
        if ($coord instanceof Station) {
            return [];
        }

        if (MeasurementInterface::MEASUREMENT_UVINDEX === $pollutantId) {
            $data = $this->retrieveUVIndexForCoord($coord);

            if (!$data) {
                return [];
            }

            return [$data];
        }

        if (MeasurementInterface::MEASUREMENT_TEMPERATURE === $pollutantId) {
            $data = $this->retrieveTemperatureForCoord($coord);

            if (!$data) {
                return [];
            }

            return [$data];
        }

        if (MeasurementInterface::MEASUREMENT_CORONAINCIDENCE === $pollutantId) {
            $data = $this->retrieveCoronaIncidenceForCoord($coord);

            if (!$data) {
                return [];
            }

            return [$data];
        }

        return [];
    }

    protected function retrieveUVIndexForCoord(CoordInterface $coord): ?Data
    {
        $jsonData = $this->owmSourceFetcher->queryUVIndex($coord);

        $value = $this->owmJsonParser->parseUVIndex($jsonData);

        $station = new Station($coord->getLatitude(), $coord->getLongitude());
        return ValueDataConverter::convert($value, $station);
    }

    protected function retrieveTemperatureForCoord(CoordInterface $coord): ?Data
    {
        $jsonData = $this->owmSourceFetcher->queryTemperature($coord);

        $value = $this->owmJsonParser->parseTemperature($jsonData);

        $station = new Station($coord->getLatitude(), $coord->getLongitude());
        return ValueDataConverter::convert($value, $station);
    }

    protected function retrieveCoronaIncidenceForCoord(CoordInterface $coord): ?Data
    {
        $jsonData = $this->coronaSourceFetcher->queryCoronaIncidence($coord);

        $value = $this->coronaJsonParser->parseCoronaIncidence($jsonData);

        $station = new Station($coord->getLatitude(), $coord->getLongitude());
        return ValueDataConverter::convert($value, $station);
    }
}
