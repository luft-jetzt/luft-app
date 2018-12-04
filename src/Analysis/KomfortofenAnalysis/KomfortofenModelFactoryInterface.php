<?php declare(strict_types=1);

namespace App\Analysis\KomfortofenAnalysis;

interface KomfortofenModelFactoryInterface
{
    public function convert(array $buckets): array;
}
