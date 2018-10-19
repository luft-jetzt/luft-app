<?php declare(strict_types=1);

namespace App\SourceFetcher\Query\UbQuery;

use AppBundle\SourceFetcher\Query\QueryInterface;

interface UbQueryInterface extends QueryInterface
{
    public function getQueryString(): string;
    public function getQueryOptions(): array;
    public function getDateTimeFormat(): string;
}
