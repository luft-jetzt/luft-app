<?php declare(strict_types=1);

namespace App\Air\SourceFetcher;

interface SourceFetcherInterface
{
    public function fetch(FetchProcess $fetchProcess): FetchResult;
}
