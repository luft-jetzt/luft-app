<?php declare(strict_types=1);

namespace App\Air\AirQuality\PollutionLevel;

abstract class AbstractPollutionLevel implements PollutionLevelInterface
{
    protected array $levels = [];

    #[\Override]
    public function getLevels(): array
    {
        return $this->levels;
    }

    #[\Override]
    public function getPollutionIdentifier(): string
    {
        $className = substr(strrchr(static::class, '\\'), 1);

        return strtolower(substr($className, 0, -5));
    }
}
