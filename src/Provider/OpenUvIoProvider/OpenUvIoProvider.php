<?php declare(strict_types=1);

namespace App\Provider\OpenUvIoProvider;

use App\Air\Measurement\UVIndex;
use App\Provider\AbstractProvider;
use App\Provider\OpenWeatherMapProvider\SourceFetcher\SourceFetcher;
use App\SourceFetcher\FetchProcess;
use App\SourceFetcher\FetchResult;

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
