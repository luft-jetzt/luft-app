<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Reporting;

/**
 * Ein-Stunden-Mittelwert
 */
class Uba1SMW extends AbstractReporting
{
    public function __construct(\DateTimeImmutable $dateTime)
    {
        $this->interval = new \DateInterval('PT1H');

        $dateTime = $dateTime->sub($this->interval);

        parent::__construct($dateTime);
    }

    public function getStartDateTime(): \DateTimeImmutable
    {
        return $this->calcLastHourStart();
    }

    public function getEndDateTime(): \DateTimeImmutable
    {
        return $this->calcLastHourEnd();
    }
}
