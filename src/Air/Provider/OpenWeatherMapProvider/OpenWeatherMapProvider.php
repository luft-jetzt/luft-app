<?php declare(strict_types=1);

namespace App\Air\Provider\OpenWeatherMapProvider;

use App\Air\Pollutant\Temperature;
use App\Air\Pollutant\UVIndex;
use App\Air\Provider\AbstractProvider;
use App\Air\Provider\OpenWeatherMapProvider\SourceFetcher\SourceFetcher;
use App\Air\SourceFetcher\FetchProcess;
use App\Air\SourceFetcher\FetchResult;

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
