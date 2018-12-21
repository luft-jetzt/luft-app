<?php declare(strict_types=1);

namespace App\Analysis\FireworksAnalysis;

interface FireworksModelFactoryInterface
{
    public function convert(array $buckets): array;
}
