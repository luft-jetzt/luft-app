<?php

namespace AppBundle\SourceFetcher\Reporting;

interface ReportingInterface
{
    public function getStartDateTime(): \DateTimeImmutable;
    public function getEndDateTime(): \DateTimeImmutable;
    public function getStartTimestamp(): int;
    public function getEndTimestamp(): int;
    public function getReportingIdentifier(): string;
}
