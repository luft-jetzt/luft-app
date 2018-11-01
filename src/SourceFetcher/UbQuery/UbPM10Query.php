<?php declare(strict_types=1);

namespace App\SourceFetcher\UbQuery;

class UbPM10Query extends AbstractUbQuery
{
    public function getDateTimeFormat(): string
    {
        return 'd.m.Y';
    }
}
