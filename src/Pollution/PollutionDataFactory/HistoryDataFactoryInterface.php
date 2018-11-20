<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

interface HistoryDataFactoryInterface extends PollutionDataFactoryInterface
{
    public function createDecoratedPollutantListForInterval(\DateTime $fromDateTime, \DateTime $untilDateTime): array;
    public function getDataListsForInterval(\DateTime $fromDateTime, \DateTime $untilDateTime): array;
}
