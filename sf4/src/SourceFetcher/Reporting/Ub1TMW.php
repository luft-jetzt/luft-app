<?php declare(strict_types=1);

namespace App\SourceFetcher\Reporting;

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
