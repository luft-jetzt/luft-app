<?php

namespace AppBundle\SourceFetcher\Parser;

use AppBundle\SourceFetcher\Query\QueryInterface;
use AppBundle\SourceFetcher\Value\Value;

class UbParser
{
    const STATION = 0;
    const DATETIME = 5;
    const VALUE = 6;

    protected $query = null;

    public function __construct(QueryInterface $query)
    {
        $this->query = $query;
    }

    public function parse(string $string, string $pollutant): array
    {
        $lines = explode(PHP_EOL, $string);
        $valueList = [];

        array_shift($lines); // remove column headlines

        foreach ($lines as $line) {
            $line = str_replace('"', '', $line);

            $parts = explode(';', $line);

            if (count($parts) <= 1) {
                continue;
            }

            $dataValue = new Value();

            try {
                $dateTimeFormat = $this->query->getDateTimeFormat();

                $station = $parts[self::STATION];
                $dateTime = \DateTime::createFromFormat($dateTimeFormat, $parts[self::DATETIME]);
                $value = $parts[self::VALUE];

                var_dump($dateTimeFormat);
                if (!$station || !$dateTime || !$value) {
                    continue;
                }

                $dataValue
                    ->setStation($station)
                    ->setDateTime($dateTime)
                    ->setPollutant($pollutant)
                    ->setValue($value)
                ;

            } catch (\Exception $e) {
                var_dump($e);
            }

            $valueList[] = $dataValue;
        }

        return $valueList;
    }
}
