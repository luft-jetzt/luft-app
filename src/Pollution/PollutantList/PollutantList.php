<?php declare(strict_types=1);

namespace App\Pollution\PollutantList;

use App\Pollution\Pollutant\PollutantInterface;

class PollutantList implements PollutantListInterface
{
    /** @var array $list */
    protected $list;

    public function addPollutant(PollutantInterface $pollutant): PollutantListInterface
    {
        $this->list[$pollutant->getIdentifier()] = $pollutant;

        return $this;
    }

    public function getPollutants(): array
    {
        return $this->list;
    }
}
