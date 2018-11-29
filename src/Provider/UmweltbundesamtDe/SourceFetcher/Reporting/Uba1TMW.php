<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Reporting;

/**
 * Tagesmittelwert
 */
class Uba1TMW extends AbstractReporting
{
    public function __construct(\DateTimeImmutable $endDateTime, \DateTimeImmutable $startDateTime = null)
    {
        $this->interval = new \DateInterval('P1D');

        parent::__construct($endDateTime, $startDateTime);
    }
}
