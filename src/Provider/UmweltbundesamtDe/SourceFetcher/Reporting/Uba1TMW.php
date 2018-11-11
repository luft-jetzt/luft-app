<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Reporting;

/**
 * Tagesmittelwert
 */
class Uba1TMW extends AbstractReporting
{
    public function getStartDateTime(): \DateTimeImmutable
    {
        $this->interval = new \DateInterval('P1D');
    }
}
