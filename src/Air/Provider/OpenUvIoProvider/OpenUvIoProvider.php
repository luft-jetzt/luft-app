<?php declare(strict_types=1);

namespace App\Air\Provider\OpenUvIoProvider;

use App\Air\Pollutant\UVIndex;
use App\Air\Provider\AbstractProvider;
use App\Air\Provider\OpenWeatherMapProvider\SourceFetcher\SourceFetcher;
use App\Air\SourceFetcher\FetchProcess;
use App\Air\SourceFetcher\FetchResult;

class OpenUvIoProvider extends AbstractProvider
{
    final public const string IDENTIFIER = 'ouvio';

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
            UVIndex::class,
        ];
    }

    #[\Override]
    public function fetchMeasurements(FetchProcess $fetchProcess): FetchResult
    {
        return $this->sourceFetcher->fetch($fetchProcess);
    }
}
