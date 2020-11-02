<?php declare(strict_types=1);

namespace App\SourceFetcher;

class FetchResult
{
    protected array $counters = [];

    public function incCounter(string $identifier, int $step = 1): FetchResult
    {
        if (!array_key_exists($identifier, $this->counters)) {
            $this->counters[$identifier] = 0;
        }

        $this->counters[$identifier] += $step;

        return $this;
    }

    public function setCounter(string $identifier, int $counter): FetchResult
    {
        $this->counters[$identifier] = $counter;

        return $this;
    }

    public function getCounters(): array
    {
        return $this->counters;
    }
}
