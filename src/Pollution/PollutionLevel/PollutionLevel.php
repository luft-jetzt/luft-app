<?php declare(strict_types=1);

namespace App\Pollution\PollutionLevel;

use App\Entity\Data;

/** @deprecated  */
class PollutionLevel
{
    const LEVEL_ACCEPTABLE = 1;
    const LEVEL_WARNING = 2;
    const LEVEL_DANGER = 3;
    const LEVEL_DEATH = 4;

    protected $levels = [];

    public function __construct(int $acceptable, int $warning, int $danger, int $death)
    {
        $this->levels = [
            self::LEVEL_ACCEPTABLE => $acceptable,
            self::LEVEL_WARNING => $warning,
            self::LEVEL_DANGER => $danger,
            self::LEVEL_DEATH => $death,
        ];
    }

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
