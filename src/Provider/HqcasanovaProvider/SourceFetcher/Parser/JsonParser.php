<?php declare(strict_types=1);

namespace App\Provider\HqcasanovaProvider\SourceFetcher\Parser;

use App\Air\Measurement\MeasurementInterface;
use App\Entity\Station;
use App\Pollution\Value\Value;

class JsonParser implements JsonParserInterface
{
    protected function prepareStation(): Station
    {
        $station = new Station(19.536342, -155.576480);
        $station
            ->setAltitude(3397)
            ->setStationCode('USHIMALO')
            ->setTitle('Mauna Loa Observatory');

        return $station;
    }

    public function parse(string $jsonData): array
    {
        $jsonData = str_replace(['process(', '"})'], ['', '"}'], $jsonData);
        $co2Value = json_decode($jsonData);

        $value = new Value();
        $value->setValue((float) $co2Value->{'0'})
            ->setPollutant(MeasurementInterface::MEASUREMENT_CO2)
            ->setDateTime(new \DateTime($co2Value->date))
            ->setStation('USHIMALO');

        return [$value];
    }
}
