<?php

namespace AppBundle\SourceFetcher\Reporting;

/**
 * Tagesmittelwert
 */
class Ub1TMW extends AbstractUbReporting
{
    public function getStartDateTime(): \DateTimeImmutable
    {
        return $this->calcLastDayStart();
    }

    public function getEndDateTime(): \DateTimeImmutable
    {
        return $this->calcLastDayEnd();
    }
}
