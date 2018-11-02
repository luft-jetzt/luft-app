<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Query;

interface UbaQueryInterface
{
    public function getQueryString(): string;
    public function getQueryOptions(): array;
    public function getDateTimeFormat(): string;
}
