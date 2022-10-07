<?php declare(strict_types=1);

namespace App\Provider\OpenWeatherMapProvider;

use App\Air\Measurement\Temperature;
use App\Air\Measurement\UVIndex;
use App\Provider\AbstractProvider;
use App\Provider\OpenWeatherMapProvider\SourceFetcher\SourceFetcher;
use App\SourceFetcher\FetchProcess;
use App\SourceFetcher\FetchResult;
use Caldera\GeoBasic\Coord\Coord;

class OpenWeatherMapProvider extends AbstractProvider
{
    final const IDENTIFIER = 'owm';

    public function __construct(protected SourceFetcher $sourceFetcher)
    {
    }

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    public function providedMeasurements(): array
    {
        return [
            Temperature::class,
            UVIndex::class,
        ];
    }

    public function fetchMeasurements(FetchProcess $fetchProcess): FetchResult
    {
        return $this->sourceFetcher->fetch($fetchProcess);
    }
}
