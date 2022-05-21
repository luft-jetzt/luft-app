<?php declare(strict_types=1);

namespace App\Pollution\DataCache;

use App\Entity\Data;
use App\Entity\Station;

class KeyGenerator
{
    private function __construct()
    {

    }

    public static function generateKeyForData(Data $data): string
    {
        return sprintf('luft-data-%d-%d', $data->getStationId(), $data->getPollutant());
    }

    public static function generateKeyForStationAndPollutant(Station $station, int $pollutant): string
    {
        return sprintf('luft-data-%d-%d', $station->getId(), $pollutant);
    }
}
