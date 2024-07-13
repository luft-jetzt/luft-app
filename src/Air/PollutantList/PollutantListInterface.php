<?php declare(strict_types=1);

namespace App\Air\PollutantList;

use App\Air\Pollutant\PollutantInterface;

interface PollutantListInterface
{
    public function addPollutant(PollutantInterface $pollutant): PollutantListInterface;
    public function getPollutants(): array;
    public function getPollutantListWithIds(): array;
}
