<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Filter;

class NoopFilter implements FilterInterface
{
    public function filter(float $value = null): ?float
    {
        return $value;
    }
}
