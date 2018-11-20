<?php declare(strict_types=1);

namespace App\Provider\EuropeanEnvironmentAgency\SourceFetcher\Parser;

use App\Pollution\PollutantList\PollutantListInterface;
use App\Pollution\Value\Value;
use League\Csv\Reader;

class CsvParser implements CsvParserInterface
{
    /** @var PollutantListInterface $pollutantList */
    protected $pollutantList;

    public function __construct(PollutantListInterface $pollutantList)
    {
        $this->pollutantList = $pollutantList;
    }

    public function parse(string $csvFileContent): array
    {
        $valueList = [];

        $csv = Reader::createFromString(utf8_decode($csvFileContent));

        $csv
            ->setDelimiter(',')
            ->setHeaderOffset(0);

        foreach ($csv as $dataLine) {
            if (!$this->checkRequiredFields($dataLine)) {
                continue;
            }

            $dateTime = new \DateTime($dataLine['value_datetime_begin']);
            $stationCode = $dataLine['station_code'];
            $floatValue = (float) $dataLine['value_numeric'];
            $pollutantId = $this->pollutantList->getPollutantId(strtolower($dataLine['pollutant']));

            $value = new Value();
            $value->setPollutant($pollutantId)
                ->setDateTime($dateTime)
                ->setStation($stationCode)
                ->setValue($floatValue);

            $valueList[] = $value;
        }

        return $valueList;
    }

    protected function checkRequiredFields(array $dataLine): bool
    {
        $requiredFields = ['value_datetime_begin', 'value_numeric', 'pollutant', 'station_code'];

        foreach ($requiredFields as $requiredField) {
            if (!array_key_exists($requiredField, $dataLine) || !$dataLine[$requiredField]) {
                return false;
            }
        }

        return true;
    }
}
