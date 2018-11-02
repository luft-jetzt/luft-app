<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\Reporting;

/**
 * Ein-Stunden-Tagesmaxima
 */
class SMWMAX extends AbstractReporting
{
    public function __construct(\DateTimeImmutable $dateTime)
    {
        $dateTime = $dateTime->sub(new \DateInterval('PT1H'));

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
