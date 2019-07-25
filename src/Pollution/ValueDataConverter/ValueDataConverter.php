<?php declare(strict_types=1);

namespace App\Pollution\ValueDataConverter;

use App\Entity\Data;
use App\Entity\Station;
use App\Pollution\Value\Value;

class ValueDataConverter
{
    private function __construct()
    {

    }

    public static function convert(Value $value, Station $station = null): Data
    {
        $data = new Data();

        $data
            ->setDateTime($value->getDateTime())
            ->setValue($value->getValue())
            ->setPollutant($value->getPollutant());

        if ($station) {
            $data->setStation($station);
        }

        return $data;
    }
}