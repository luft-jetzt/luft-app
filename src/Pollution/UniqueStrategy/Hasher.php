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
        return sprintf('%d-%d-%d-%f',
            $data->getStationId(),
            $data->getDateTime()->format('U'),
            $data->getPollutant(),
            $data->getValue()
        );
    }

    public static function hashValue(Value $value): string
    {
        return sprintf('%d-%d-%d-%f',
            $value->getStation(),
            $value->getDateTime()->format('U'),
            $value->getPollutant(),
            $value->getValue()
        );
    }
}
