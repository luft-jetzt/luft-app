<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever;

use App\Air\Measurement\MeasurementInterface;
use App\Entity\Data;
use App\Entity\Station;
use App\Pollution\ValueDataConverter\ValueDataConverter;
use App\Provider\OpenWeatherMapProvider\SourceFetcher\Parser\JsonParserInterface;
use App\Provider\OpenWeatherMapProvider\SourceFetcher\SourceFetcher;
use Caldera\GeoBasic\Coord\CoordInterface;

class AdhocDataRetriever implements DataRetrieverInterface
{
    /** @var SourceFetcher $sourceFetcher */
    protected $sourceFetcher;

    /** @var JsonParserInterface $jsonParser */
    protected $jsonParser;

    public function __construct(SourceFetcher $sourceFetcher, JsonParserInterface $jsonParser)
    {
        $this->sourceFetcher = $sourceFetcher;
        $this->jsonParser = $jsonParser;
    }

    public function retrieveDataForCoord(CoordInterface $coord, int $pollutantId, \DateTime $fromDateTime = null, \DateInterval $dateInterval = null, float $maxDistance = 20.0, int $maxResults = 250): array
    {
        if ($coord instanceof Station) {
            return [];
        }

        if (MeasurementInterface::MEASUREMENT_UV === $pollutantId) {
            $data = $this->retrieveUVIndexForCoord($coord);

            return [$data];
        }

        if (MeasurementInterface::MEASUREMENT_TEMPERATURE === $pollutantId) {
            $data = $this->retrieveTemperatureForCoord($coord);

            return [$data];
        }

        return [];
    }

    protected function retrieveUVIndexForCoord(CoordInterface $coord): Data
    {
        $jsonData = $this->sourceFetcher->queryUVIndex($coord);

        $value = $this->jsonParser->parseUVIndex($jsonData);

        $station = new Station($coord->getLatitude(), $coord->getLongitude());
        return ValueDataConverter::convert($value, $station);
    }

    protected function retrieveTemperatureForCoord(CoordInterface $coord): Data
    {
        $jsonData = $this->sourceFetcher->queryTemperature($coord);

        $value = $this->jsonParser->parseTemperature($jsonData);

        $station = new Station($coord->getLatitude(), $coord->getLongitude());
        return ValueDataConverter::convert($value, $station);
    }
}
