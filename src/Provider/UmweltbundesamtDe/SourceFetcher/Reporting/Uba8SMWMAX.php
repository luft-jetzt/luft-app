<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Reporting;

/**
 * Acht-Stunden-Tagesmaxima
 */
class Uba8SMWMAX extends AbstractReporting
{
    public function __construct(\DateTimeImmutable $dateTime)
    {
        $this->interval = new \DateInterval('PT8H');

        parent::__construct($dateTime);
    }

    public function getStartDateTime(): \DateTimeImmutable
    {
        return $this->calcLastDayStart();
    }

    public function getEndDateTime(): \DateTimeImmutable
    {
        return $this->calcLastDayEnd();
    }
}
