<?php declare(strict_types=1);

namespace App\Air\DataRetriever;

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
        private readonly OwmJsonParserInterface $owmJsonParser
    )
    {

    }

    #[\Override]
    public function retrieveDataForCoord(CoordInterface $coord): array
    {
        if ($coord instanceof Station) {
            return [];
        }

        $temperature = $this->retrieveTemperatureForCoord($coord);

        $dataList = [$temperature];

        return array_filter($dataList);
    }

    protected function retrieveTemperatureForCoord(CoordInterface $coord): ?Data
    {
        $jsonData = $this->owmSourceFetcher->queryTemperature($coord);

        $value = $this->owmJsonParser->parseTemperature($jsonData);

        $station = new Station($coord->getLatitude(), $coord->getLongitude());
        return ValueDataConverter::convert($value, $station);
    }
}
