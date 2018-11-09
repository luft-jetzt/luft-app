<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

interface HistoryDataFactoryInterface extends PollutionDataFactoryInterface
{
    public function getDataListsForInterval(\DateTime $fromDateTime, \DateTime $untilDateTime): array;
}
