<?php declare(strict_types=1);

namespace App\Provider\OpenWeatherMapProvider\SourceFetcher\Parser;

use App\Pollution\Value\Value;

class JsonParser implements JsonParserInterface
{
    public function parseUVIndex(string $jsonData): Value
    {
        $uvValue = json_decode($jsonData, null, 512, JSON_THROW_ON_ERROR);

        $value = new Value();
        $value->setValue((float) $uvValue->value)
            ->setPollutant('uvindex')
            ->setDateTime(new \DateTime(sprintf('@%d', $uvValue->date), new \DateTimeZone('UTC')));

        return $value;
    }

    public function parseTemperature(string $jsonData): Value
    {
        $temperatureValue = json_decode($jsonData, null, 512, JSON_THROW_ON_ERROR);

        $value = new Value();
        $value->setValue((float) $temperatureValue->main->temp)
            ->setPollutant('temperature')
            ->setDateTime(new \DateTime(sprintf('@%d', $temperatureValue->dt), new \DateTimeZone('UTC')));

        return $value;
    }
}
