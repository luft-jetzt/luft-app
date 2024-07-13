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
    final public const string IDENTIFIER = 'owm';

    public function __construct(protected SourceFetcher $sourceFetcher)
    {
    }

    #[\Override]
    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    #[\Override]
    public function providedMeasurements(): array
    {
        return [
            Temperature::class,
            UVIndex::class,
        ];
    }

    #[\Override]
    public function fetchMeasurements(FetchProcess $fetchProcess): FetchResult
    {
        return $this->sourceFetcher->fetch($fetchProcess);
    }
}
