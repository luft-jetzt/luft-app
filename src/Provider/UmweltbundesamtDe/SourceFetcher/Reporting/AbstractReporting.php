<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Reporting;

use App\Util\DateTimeUtil;

abstract class AbstractReporting implements ReportingInterface
{
    /** @var \DateTimeImmutable $startDateTime */
    protected $startDateTime;

    /** @var \DateTimeImmutable $endDateTime */
    protected $endDateTime;

    /** @var \DateInterval $interval */
    protected $interval;

    public function __construct(\DateTimeImmutable $endDateTime, \DateTimeImmutable $startDateTime = null)
    {
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;
    }

    public function getStartDateTime(): \DateTimeImmutable
    {
        if ($this->startDateTime) {
            return DateTimeUtil::getHourStartDateTime($this->startDateTime);
        }

        return DateTimeUtil::getHourStartDateTime($this->endDateTime->sub($this->interval));
    }

    public function getStartTimestamp(): int
    {
        return (int) $this->getStartDateTime()->format('U');
    }

    public function getEndDateTime(): \DateTimeImmutable
    {
        return DateTimeUtil::getHourStartDateTime($this->endDateTime);
    }

    public function getEndTimestamp(): int
    {
        return (int) $this->getEndDateTime()->format('U');
    }

    public function getDateInterval(): \DateInterval
    {
        return $this->interval;
    }

    public function getReportingIdentifier(): string
    {
        $reflection = new \ReflectionClass($this);
        $identifier = $reflection->getShortName();

        $identifier = str_replace('Uba', '', $identifier);
        $identifier = str_replace('MAX', '_MAX', $identifier);

        return $identifier;
    }
}
