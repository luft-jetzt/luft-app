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

    public function getPollutant(string $identifier): ?PollutantInterface
    {
        if (!array_key_exists($identifier, $this->list)) {
            return null;
        }

        return $this->list[$identifier];
    }

    public function getPollutantId(string $identifier): ?int
    {
        if (!$pollutant = $this->getPollutant($identifier)) {
            return null;
        }

        return constant(sprintf('App\Pollution\Pollutant\PollutantInterface::POLLUTANT_%s', strtoupper($pollutant->getIdentifier())));
    }

    public function getPollutants(): array
    {
        return $this->list;
    }

    public function getPollutantsWithIds(): array
    {
        $pollutantIdList = [];

        /** @var PollutantInterface $pollutant */
        foreach ($this->list as $pollutant) {
            $pollutantId = constant(sprintf('App\\Pollution\\Pollutant\\PollutantInterface::POLLUTANT_%s', strtoupper($pollutant->getIdentifier())));

            $pollutantIdList[$pollutantId] = $pollutant;
        }

        return $pollutantIdList;
    }
}
