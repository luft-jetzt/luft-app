<?php declare(strict_types=1);

namespace App\SourceFetcher\LdQuery;

abstract class AbstractLdQuery implements LdQueryInterface
{
    public function getQueryString(): string
    {
        // TODO: Implement getQueryString() method.
    }

    public function getQueryOptions(): array
    {
        // TODO: Implement getQueryOptions() method.
    }

    public function getDateTimeFormat(): string
    {
        // TODO: Implement getDateTimeFormat() method.
    }
}
