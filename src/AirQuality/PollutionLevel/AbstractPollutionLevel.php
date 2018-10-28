<?php declare(strict_types=1);

namespace App\AirQuality\PollutionLevel;

use App\Entity\Data;

class AbstractPollutionLevel implements PollutionLevelInterface
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
}
