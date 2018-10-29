<?php declare(strict_types=1);

namespace App\AirQuality\PollutionLevel;

use App\Entity\Data;

abstract class AbstractPollutionLevel implements PollutionLevelInterface
{
    protected $levels = [];

    public function getLevel(Data $data): int
    {
        $levels = array_reverse($this->levels, true);

        $current = null;

        foreach ($levels as $level => $value) {
            if (!$current || $data->getValue() < $value) {
                $current = $level;
            }
        }

        return $current;
    }

    public function getPollutionIdentifier(): string
    {
        $reflection = new \ReflectionClass($this);

        $className = $reflection->getShortName();

        return strtolower(substr($className, 0, -5));
    }
}
