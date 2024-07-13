<?php declare(strict_types=1);

namespace App\Air\PollutantList;

use App\Air\Pollutant\PollutantInterface;

class PollutantList implements PollutantListInterface
{
    protected array $list = [];

    #[\Override]
    public function addPollutant(PollutantInterface $pollutant): PollutantListInterface
    {
        $this->list[$pollutant->getIdentifier()] = $pollutant;

        return $this;
    }

    #[\Override]
    public function getPollutants(): array
    {
        return $this->list;
    }

    #[\Override]
    public function getPollutantListWithIds(): array
    {
        $pollutantsListWithIds = [];

        /** @var PollutantInterface $pollutant */
        foreach ($this->list as $pollutant) {
            $pollutantId = constant(sprintf('App\\Air\\Pollutant\\PollutantInterface::POLLUTANT_%s', strtoupper($pollutant->getIdentifier())));

            $pollutantsListWithIds[$pollutantId] = $pollutant;
        }

        return $pollutantsListWithIds;
    }
}
