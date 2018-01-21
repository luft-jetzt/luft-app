<?php declare(strict_types=1);

namespace AppBundle\SourceFetcher\Reporting;

/**
 * Acht-Stunden-Mittelwert
 */
class Ub8SMW extends AbstractUbReporting
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
