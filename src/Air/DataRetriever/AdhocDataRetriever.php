<?php declare(strict_types=1);

namespace App\Air\DataRetriever;

use App\Air\Pollutant\PollutantInterface;
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
        private readonly OwmSourceFetcher $owmSourceFetcher,
        private readonly OwmJsonParserInterface $owmJsonParser,
        private readonly OpenUvIoJsonParserInterface $openUvIoJsonParser,
        private readonly OpenUvIoSourceFetcher $openUvIoSourceFetcher
    )
    {

    }

    #[\Override]
    public function retrieveDataForCoord(CoordInterface $coord): array
    {
        if ($coord instanceof Station) {
            return [];
        }

        $maxUvIndex = $this->retrieveUVIndexMaxForCoord($coord);
        $temperature = $this->retrieveTemperatureForCoord($coord);

        $dataList = [$maxUvIndex, $temperature];

        return array_filter($dataList);
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
