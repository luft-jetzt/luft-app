<?php declare(strict_types=1);

namespace App\Provider;

interface SourceFetcherInterface
{
    public function query(QueryInterface $query = null);
}
