<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Filter;

interface FilterInterface
{
    public function filter(float $value = null): ?float;
}
