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

        parent::__construct($dateTime);
    }
}
