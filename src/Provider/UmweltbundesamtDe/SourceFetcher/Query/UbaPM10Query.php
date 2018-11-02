<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Query;

class UbaPM10Query extends AbstractUbaQuery
{
    public function getDateTimeFormat(): string
    {
        return 'd.m.Y';
    }
}
