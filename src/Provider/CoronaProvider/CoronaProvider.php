<?php declare(strict_types=1);

namespace App\Provider\CoronaProvider;

use App\Air\Measurement\CoronaIncidence;
use App\Air\Measurement\Temperature;
use App\Air\Measurement\UVIndex;
use App\Provider\AbstractProvider;
use App\Provider\OpenWeatherMapProvider\SourceFetcher\SourceFetcher;
use App\SourceFetcher\FetchProcess;
use App\SourceFetcher\FetchResult;

class CoronaProvider extends AbstractProvider
{
    const IDENTIFIER = 'corona';

    protected SourceFetcher $sourceFetcher;

    public function __construct(SourceFetcher $sourceFetcher)
    {
        $this->sourceFetcher = $sourceFetcher;
    }

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    public function providedMeasurements(): array
    {
        return [
            CoronaIncidence::class,
        ];
    }

    public function fetchMeasurements(FetchProcess $fetchProcess): FetchResult
    {
        return $this->sourceFetcher->fetch($fetchProcess);
    }
}
