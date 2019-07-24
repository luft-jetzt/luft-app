<?php declare(strict_types=1);

namespace App\Provider\OpenWeatherMapProvider\SourceFetcher\Parser;

use App\Air\Measurement\MeasurementInterface;
use App\Entity\Station;
use App\Pollution\Value\Value;

class JsonParser implements JsonParserInterface
{
    public function parse(string $jsonData): array
    {
        $uvValue = json_decode($jsonData);

        $value = new Value();
        $value->setValue((float) $uvValue->value)
            ->setPollutant(MeasurementInterface::MEASUREMENT_UV)
            ->setDateTime(new \DateTimeImmutable(sprintf('@%d', $uvValue->date)));

        return [$value];
    }
}
