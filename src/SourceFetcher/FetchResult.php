<?php declare(strict_types=1);

namespace App\SourceFetcher;

class FetchResult
{
    protected int $counter = 0;

    public function incCounter(int $step = 1): FetchResult
    {
        $this->counter += $step;

        return $this;
    }

    public function setCounter(int $counter): FetchResult
    {
        $this->counter = $counter;

        return $this;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }
}
