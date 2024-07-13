<?php declare(strict_types=1);

namespace App\Air\PollutionDataFactory;

interface HistoryDataFactoryInterface
{
    public function createDecoratedPollutantListForInterval(\DateTime $fromDateTime, \DateTime $untilDateTime): array;
}
