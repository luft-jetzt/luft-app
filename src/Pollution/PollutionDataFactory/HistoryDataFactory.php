<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

class HistoryDataFactory extends PollutionDataFactory
{
    public function getDataListsForInterval(\DateTime $fromDateTime, \DateTime $endDateTime): array
    {
        $hour = new \DateInterval('PT1H');


    }

}
