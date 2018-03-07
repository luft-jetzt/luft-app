<?php declare(strict_types=1);

namespace App\SourceFetcher\Query;

class UbPM10Query extends AbstractQuery
{
    public function getDateTimeFormat(): string
    {
        return 'd.m.Y';
    }
}
