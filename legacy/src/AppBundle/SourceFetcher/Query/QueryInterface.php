<?php declare(strict_types=1);

namespace AppBundle\SourceFetcher\Query;

interface QueryInterface
{
    public function getQueryString(): string;
    public function getQueryOptions(): array;
    public function getDateTimeFormat(): string;
}