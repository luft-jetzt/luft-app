<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\Query;

class PM10Query extends AbstractQuery
{
    public function getDateTimeFormat(): string
    {
        return 'd.m.Y';
    }
}
