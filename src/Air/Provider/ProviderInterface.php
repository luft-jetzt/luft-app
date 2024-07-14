<?php declare(strict_types=1);

namespace App\Air\Provider;

use App\Air\Pollutant\PollutantInterface;
use App\Air\SourceFetcher\FetchProcess;
use App\Air\SourceFetcher\FetchResult;

interface ProviderInterface
{
    public function getIdentifier(): string;

    public function getStationLoader(): StationLoaderInterface;

    public function providedPollutants(): array;

    public function providesPollutant(PollutantInterface $pollutant): bool;

    public function fetchPollutant(FetchProcess $fetchProcess): FetchResult;
}
