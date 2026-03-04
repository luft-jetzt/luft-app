<?php declare(strict_types=1);

namespace App\Air\ValueDataConverter;

use App\Air\Pollutant\PollutantInterface;
use Caldera\LuftModel\Model\Value;
use App\Entity\Data;
use App\Entity\Station;

class ValueDataConverter
{
    private const array POLLUTANT_MAP = [
        'pm10' => PollutantInterface::POLLUTANT_PM10,
        'pm25' => PollutantInterface::POLLUTANT_PM25,
        'o3' => PollutantInterface::POLLUTANT_O3,
        'no2' => PollutantInterface::POLLUTANT_NO2,
        'so2' => PollutantInterface::POLLUTANT_SO2,
        'co' => PollutantInterface::POLLUTANT_CO,
        'co2' => PollutantInterface::POLLUTANT_CO2,
        'uvindex' => PollutantInterface::POLLUTANT_UVINDEX,
        'temperature' => PollutantInterface::POLLUTANT_TEMPERATURE,
        'uvindexmax' => PollutantInterface::POLLUTANT_UVINDEXMAX,
    ];

    private function __construct()
    {

    }

    public static function convert(Value $value, ?Station $station = null): ?Data
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
        $key = strtolower(str_replace('_', '', $pollutantIdentifier));

        return self::POLLUTANT_MAP[$key] ?? null;
    }
}
