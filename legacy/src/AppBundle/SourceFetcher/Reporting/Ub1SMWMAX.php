<?php declare(strict_types=1);

namespace AppBundle\SourceFetcher\Reporting;

/**
 * Ein-Stunden-Tagesmaxima
 */
class Ub1SMWMAX extends AbstractUbReporting
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
