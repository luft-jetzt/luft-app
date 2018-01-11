<?php

namespace AppBundle\SourceFetcher\Reporting;

abstract class AbstractUbReporting implements ReportingInterface
{
    /** @var \DateTimeInterface $dateTime */
    protected $dateTime;

    public function __construct(\DateTimeInterface $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function getStartTimestamp(): int
    {
        return $this->getStartDateTime()->format('U');
    }

    public function getEndTimestamp(): int
    {
        return $this->getEndDateTime()->format('U');
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
        $dateTimeSpec = $this->dateTime->format('Y-m-d H:00:00');

        return new \DateTimeImmutable($dateTimeSpec);
    }
}
