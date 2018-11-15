<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Filter;

class COFilter implements FilterInterface
{
    /**
     * Sometimes CO values are delivered as milligramm instead of microgramm.
     */
    public function filter(float $value = null): ?float
    {
        if ($value && $value < 1000.00) {
            return $value * 1000;
        }

        return $value;
    }
}
