<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Reporting;

abstract class AbstractReporting implements ReportingInterface
{
    /** @var \DateTime $dateTime */
    protected $dateTime;

    public function __construct(\DateTimeImmutable $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function getStartTimestamp(): int
    {
        return (int) $this->getStartDateTime()->format('U');
    }

    public function getEndTimestamp(): int
    {
        return (int) $this->getEndDateTime()->format('U');
    }

    protected function calcLastHourStart(): \DateTimeImmutable
    {
        $interval = new \DateInterval('PT1H');

        $lastHourEnd = $this->calcLastHourEnd();

        return $lastHourEnd->sub($interval);
    }

    protected function calcLastHourEnd(): \DateTimeImmutable
    {
        $dateTimeSpec = $this->dateTime->format('Y-m-d H:00:00');

        return new \DateTimeImmutable($dateTimeSpec);
    }

    protected function calcLastDayStart(): \DateTimeImmutable
    {
        $interval = new \DateInterval('P1D');

        $lastDayEnd = $this->calcLastDayEnd();

        return $lastDayEnd->sub($interval);
    }

    protected function calcLastDayEnd(): \DateTimeImmutable
    {
        $dateTimeSpec = $this->dateTime->format('Y-m-d 00:00:00');

        return new \DateTimeImmutable($dateTimeSpec);
    }

    public function getReportingIdentifier(): string
    {
        $reflection = new \ReflectionClass($this);
        $identifier = $reflection->getShortName();

        $identifier = str_replace('Ub', '', $identifier);
        $identifier = str_replace('MAX', '_MAX', $identifier);

        return $identifier;
    }
}
