<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever;

use App\Entity\Station;
use App\Pollution\ValueDataConverter\ValueDataConverter;
use App\Provider\OpenWeatherMapProvider\SourceFetcher\Parser\JsonParserInterface;
use App\Provider\OpenWeatherMapProvider\SourceFetcher\SourceFetcher;
use Caldera\GeoBasic\Coord\CoordInterface;

class AdhocDataRetriever implements DataRetrieverInterface
{
    /** @var SourceFetcher $sourceFetcher */
    protected $sourceFetcher;

    /** @var JsonParserInterface */
    protected $jsonParser;

    public function __construct(SourceFetcher $sourceFetcher, JsonParserInterface $jsonParser)
    {
        $this->sourceFetcher = $sourceFetcher;
        $this->jsonParser = $jsonParser;
    }

    public function retrieveDataForCoord(CoordInterface $coord, int $pollutantId, \DateTime $fromDateTime = null, \DateInterval $dateInterval = null, float $maxDistance = 20.0, int $maxResults = 250): array
    {
        $jsonData = $this->sourceFetcher->query($coord);

        $valueList = $this->jsonParser->parse($jsonData);

        $value = array_pop($valueList);

        $station = new Station($coord->getLatitude(), $coord->getLongitude());
        $data = ValueDataConverter::convert($value, $station);

        return [$data];
    }
}
