<?php declare(strict_types=1);

namespace App\Provider\Luftdaten\SourceFetcher\Parser;

use App\Pollution\Pollutant\PollutantInterface;
use App\Provider\Luftdaten\SourceFetcher\Value\Value;

class Parser implements ParserInterface
{
    protected $stationList = [];

    public function parse(array $dataList): array
    {
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
