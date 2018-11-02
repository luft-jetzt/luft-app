<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\Query;

interface QueryInterface
{
    public function getQueryString(): string;
    public function getQueryOptions(): array;
    public function getDateTimeFormat(): string;
}
