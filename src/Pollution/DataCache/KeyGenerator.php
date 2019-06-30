<?php declare(strict_types=1);

namespace App\Pollution\DataCache;

use App\Entity\Data;

class KeyGenerator
{
    private function __construct()
    {

    }

    public static function generateKey(Data $data): string
    {
        return sprintf('luft-data-%d-%d', $data->getStationId(), $data->getPollutant());
    }
}