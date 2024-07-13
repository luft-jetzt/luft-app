<?php declare(strict_types=1);

namespace App\Air\ValueDataConverter;

use App\Air\Measurement\MeasurementInterface;
use App\Air\Value\Value;
use App\Entity\Data;
use App\Entity\Station;

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

        $pollutantIdentifier = str_replace('_', '', $pollutantIdentifier);

        $expectedClassConstantName = sprintf('MEASUREMENT_%s', strtoupper($pollutantIdentifier));

        $classConstantList = $reflectionClass->getConstants();

        if (array_key_exists($expectedClassConstantName, $classConstantList)) {
            return $classConstantList[$expectedClassConstantName];
        }

        return null;
    }
}
