<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Reporting;

/**
 * Ein-Stunden-Tagesmaxima
 */
class Uba1SMWMAX extends AbstractReporting
{
    public function __construct(\DateTimeImmutable $dateTime)
    {
        $this->interval = new \DateInterval('PT1H');

        $dateTime = $dateTime->sub($this->interval);

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
