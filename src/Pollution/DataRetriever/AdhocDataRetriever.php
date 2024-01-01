<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever;

use App\Air\Measurement\MeasurementInterface;
use App\Entity\Data;
use App\Entity\Station;
use App\Pollution\ValueDataConverter\ValueDataConverter;
use App\Provider\OpenWeatherMapProvider\SourceFetcher\Parser\JsonParserInterface as OwmJsonParserInterface;
use App\Provider\OpenWeatherMapProvider\SourceFetcher\SourceFetcher as OwmSourceFetcher;
use App\Provider\OpenUvIoProvider\SourceFetcher\Parser\JsonParserInterface as OpenUvIoJsonParserInterface;
use App\Provider\OpenUvIoProvider\SourceFetcher\SourceFetcher as OpenUvIoSourceFetcher;
use Caldera\GeoBasic\Coord\CoordInterface;

class AdhocDataRetriever implements DataRetrieverInterface
{
    public function __construct(
        protected OwmSourceFetcher $owmSourceFetcher,
        protected OwmJsonParserInterface $owmJsonParser,
        protected OpenUvIoJsonParserInterface $openUvIoJsonParser,
        protected OpenUvIoSourceFetcher $openUvIoSourceFetcher
    )
    {

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

        if (MeasurementInterface::MEASUREMENT_UVINDEXMAX === $pollutantId) {
            $data = $this->retrieveUVIndexMaxForCoord($coord);

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

        return [];
    }

    protected function retrieveUVIndexForCoord(CoordInterface $coord): ?Data
    {
        $jsonData = $this->openUvIoSourceFetcher->queryUVIndex($coord);

        $value = $this->openUvIoJsonParser->parseUVIndex($jsonData);

        $station = new Station($coord->getLatitude(), $coord->getLongitude());
        return ValueDataConverter::convert($value, $station);
    }

    protected function retrieveUVIndexMaxForCoord(CoordInterface $coord): ?Data
    {
        $jsonData = $this->openUvIoSourceFetcher->queryUVMaxIndex($coord);

        $value = $this->openUvIoJsonParser->parseUVIndexMax($jsonData);

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
}
