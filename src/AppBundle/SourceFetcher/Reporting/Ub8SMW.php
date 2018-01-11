<?php

namespace AppBundle\SourceFetcher\Reporting;

/**
 * Acht-Stunden-Mittelwert
 */
class Ub8SMW extends AbstractUbReporting
{
    public function getStartDateTime(): \DateTimeImmutable
    {
        return $this->calcLastHourStart();
    }

    public function getEndDateTime(): \DateTimeImmutable
    {
        return $this->calcLastHourEnd();
    }
}
