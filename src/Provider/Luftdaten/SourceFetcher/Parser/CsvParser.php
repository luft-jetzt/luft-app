<?php declare(strict_types=1);

namespace App\Provider\Luftdaten\SourceFetcher\Parser;

use App\Pollution\Pollutant\PollutantInterface;
use App\Pollution\Value\Value;
use League\Csv\Reader;

class CsvParser implements CsvParserInterface
{
    public function parse(string $csvFileContent): array
    {
        $valueList = [];

        $csv = Reader::createFromString(utf8_decode($csvFileContent));

        $csv
            ->setDelimiter(';')
            ->setHeaderOffset(0);

        if (!$this->checkHeaderColumns($csv->getHeader())) {
            return [];
        }

        foreach ($csv as $dataLine) {
            $dateTime = new \DateTime($dataLine['timestamp']);
            $stationCode = sprintf('LFTDTN%d', $dataLine['location']);

            $pm10Value = new Value();
            $pm10Value->setPollutant(PollutantInterface::POLLUTANT_PM10)
                ->setDateTime($dateTime)
                ->setStation($stationCode)
                ->setValue((float) $dataLine['P1']);

            $pm25Value = new Value();
            $pm25Value->setPollutant(PollutantInterface::POLLUTANT_PM25)
                ->setDateTime($dateTime)
                ->setStation($stationCode)
                ->setValue((float) $dataLine['P2']);

            $valueList[] = $pm10Value;
            $valueList[] = $pm25Value;
        }

        return $valueList;
    }

    protected function checkHeaderColumns(array $headerColumns): bool
    {
        $requiredColumns = [
            'timestamp',
            'location',
            'P1',
            'P2',
        ];

        $result = true;

        foreach ($requiredColumns as $requiredColumn) {
            if (!in_array($requiredColumn, $headerColumns)) {
                $result = false;

                break;
            }
        }

        return $result;
    }
}
