<?php declare(strict_types=1);

namespace App\AirQuality\Calculator;

use App\AirQuality\PollutionLevel\PollutionLevelInterface;
use App\Pollution\Box\Box;

class AirQualityCalculator extends AbstractAirQualityCalculator
{
    public function calculateBoxList(array $boxList): int
    {
        $maxLevel = 1;

        /** @var Box $box */
        foreach ($boxList as $box) {
            $level = $this->calculateBox($box);

            if ($level > $maxLevel) {
                $maxLevel = $level;
            }
        }

        return $maxLevel;
    }

    public function calculateBox(Box $box): int
    {
        /** @var PollutionLevelInterface $level */
        $level = $this->pollutionLevels[$box->getPollutant()->getIdentifier()];

        $levelValue = $level->getLevel($box->getData());

        $box->setPollutionLevel($levelValue);

        return $levelValue;
    }
}
