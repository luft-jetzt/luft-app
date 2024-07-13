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
        $reflection = new \ReflectionClass($this);

        $className = $reflection->getShortName();

        return strtolower(substr($className, 0, -5));
    }
}
