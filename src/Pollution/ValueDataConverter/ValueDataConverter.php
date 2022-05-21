<?php declare(strict_types=1);

namespace App\Pollution\ValueDataConverter;

use App\Air\Measurement\MeasurementInterface;
use App\Entity\Data;
use App\Entity\Station;
use App\Pollution\Value\Value;

class ValueDataConverter
{
    private function __construct()
    {

    }

    public static function convert(Value $value, Station $station = null): ?Data
    {
        $data = new Data();

        $pollutantId = static::convertPollutant($value->getPollutant());

        if (!$pollutantId) {
            return null;
        }

        $data
            ->setDateTime($value->getDateTime())
            ->setValue($value->getValue())
            ->setPollutant($pollutantId)
            ->setTag($value->getTag())
        ;

        if ($station) {
            $data->setStation($station);
        }

        return $data;
    }

    protected static function convertPollutant(string $pollutantIdentifier): ?int
    {
        $reflectionClass = new \ReflectionClass(MeasurementInterface::class);

        $expectedClassConstantName = sprintf('MEASUREMENT_%s', strtoupper($pollutantIdentifier));

        $classConstantList = $reflectionClass->getConstants();

        if (array_key_exists($expectedClassConstantName, $classConstantList)) {
            return $classConstantList[$expectedClassConstantName];
        }

        return null;
    }
}
