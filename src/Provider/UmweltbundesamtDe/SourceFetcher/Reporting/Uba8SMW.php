<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Reporting;

/**
 * Acht-Stunden-Mittelwert
 */
class Uba8SMW extends AbstractReporting
{
    public function __construct(\DateTimeImmutable $dateTime)
    {
        $dateTime = $dateTime->sub(new \DateInterval('PT1H'));

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
