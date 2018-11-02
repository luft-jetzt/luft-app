<?php declare(strict_types=1);

namespace App\SourceFetcher;

use App\SourceFetcher\Query\QueryInterface;

interface SourceFetcherInterface
{
    public function query(QueryInterface $query = null): string;
}
