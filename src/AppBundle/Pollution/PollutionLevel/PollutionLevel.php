<?php

namespace AppBundle\Pollution\PollutionLevel;

class PollutionLevel
{
    const LEVEL_ACCEPTABLE = 1;
    const LEVEL_WARNING = 2;
    const LEVEL_DANGER = 3;
    const LEVEL_DEATH = 4;

    public function __construct(int $acceptable, int $warning, int $danger, int $death)
    {

    }
}
