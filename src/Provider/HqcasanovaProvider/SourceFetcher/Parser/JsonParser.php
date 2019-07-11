<?php declare(strict_types=1);

namespace App\Provider\HqcasanovaProvider\SourceFetcher\Parser;

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
        $data = json_decode($jsonData);
        
        $valueList = [];

        foreach ($dataList as $data) {
            try {
                $stationCode = sprintf('LFTDTN%d', $data->location->id);

                $dateTime = new \DateTime($data->timestamp);

                $newValueList = $this->getValues($data->sensordatavalues);

                /** @var Value $value */
                foreach ($newValueList as $value) {
                    $value
                        ->setStation($stationCode)
                        ->setDateTime($dateTime);
                }

                $valueList = array_merge($valueList, $newValueList);
            } catch (\Exception $e) {
                var_dump($e);
            }
        }

        return $valueList;
    }

    protected function getValues(array $sensorDataValues): array
    {
        $valueList = [];

        foreach ($sensorDataValues as $sensorDataValue) {
            $value = new Value();
            $value->setValue(floatval($sensorDataValue->value));

            if ($sensorDataValue->value_type === 'P1') {
                $value->setPollutant(PollutantInterface::POLLUTANT_PM10);
            } elseif ($sensorDataValue->value_type === 'P2') {
                $value->setPollutant(PollutantInterface::POLLUTANT_PM25);
            } else {
                continue;
            }

            $valueList[] = $value;
        }

        return $valueList;
    }
}
