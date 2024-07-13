<?php declare(strict_types=1);

namespace App\Air\DataRetriever;

use App\Air\Measurement\MeasurementInterface;
use App\Air\Provider\OpenUvIoProvider\SourceFetcher\Parser\JsonParserInterface as OpenUvIoJsonParserInterface;
use App\Air\Provider\OpenUvIoProvider\SourceFetcher\SourceFetcher as OpenUvIoSourceFetcher;
use App\Air\Provider\OpenWeatherMapProvider\SourceFetcher\Parser\JsonParserInterface as OwmJsonParserInterface;
use App\Air\Provider\OpenWeatherMapProvider\SourceFetcher\SourceFetcher as OwmSourceFetcher;
use App\Air\ValueDataConverter\ValueDataConverter;
use App\Entity\Data;
use App\Entity\Station;
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

    #[\Override]
    public function retrieveDataForCoord(CoordInterface $coord, int $pollutantId = null, \DateTime $fromDateTime = null, \DateInterval $dateInterval = null, float $maxDistance = 20.0, int $maxResults = 250): array
    {
        if ($coord instanceof Station) {
            return [];
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
