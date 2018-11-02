<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Reporting;

interface ReportingInterface
{
    public function getStartDateTime(): \DateTimeImmutable;
    public function getEndDateTime(): \DateTimeImmutable;
    public function getStartTimestamp(): int;
    public function getEndTimestamp(): int;
    public function getReportingIdentifier(): string;
}
