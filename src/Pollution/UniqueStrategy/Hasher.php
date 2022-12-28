<?php declare(strict_types=1);

namespace App\Pollution\UniqueStrategy;

use App\Entity\Data;
use App\Pollution\Value\Value;

class Hasher
{
    private function __construct()
    {

    }

    public static function hashData(Data $data): string
    {
        return sprintf('%s-%d-%d-%f',
            $data->getStation()->getStationCode(),
            $data->getDateTime()->format('U'),
            $data->getPollutant(),
            $data->getValue()
        );
    }

    public static function hashValue(Value $value): string
    {
        return sprintf('%s-%d-%d-%f',
            $value->getStation(),
            $value->getDateTime()->format('U'),
            $value->getPollutant(),
            $value->getValue()
        );
    }
}
