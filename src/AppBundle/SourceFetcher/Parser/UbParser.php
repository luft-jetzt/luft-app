<?php

namespace AppBundle\SourceFetcher\Parser;

use AppBundle\SourceFetcher\Value\Value;

class UbParser
{
    const STATION = 0;
    const DATETIME = 5;
    const VALUE = 6;
    const DATETIME_FORMAT = 'd.m.Y H:i';

    public function __construct()
    {

    }

    public function parse(string $string): array
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
                $station = $parts[self::STATION];
                $dateTime = \DateTime::createFromFormat(self::DATETIME_FORMAT, $parts[self::DATETIME]);
                $value = $parts[self::VALUE];

                if (!$station || !$dateTime || !$value) {
                    continue;
                }

                $dataValue
                    ->setStation($station)
                    ->setDateTime($dateTime)
                    ->setValue($value)
                ;

                echo $line;
            } catch (\Exception $e) {

            }

            $valueList[] = $dataValue;
        }

        return $valueList;
    }
}