<?php declare(strict_types=1);

namespace App\Air\Util\EntityMerger;

interface EntityMergerInterface
{
    public function merge(object $source, object $destination): object;
}
