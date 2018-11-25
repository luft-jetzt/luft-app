<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Query;

use App\Provider\UmweltbundesamtDe\SourceFetcher\Filter\FilterInterface;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Reporting\ReportingInterface;

interface UbaQueryInterface
{
    public function getQueryString(): string;
    public function getQueryOptions(): array;
    public function getDateTimeFormat(): string;
    public function getReporting(): ReportingInterface;
    public function getFilter(): FilterInterface;
}
