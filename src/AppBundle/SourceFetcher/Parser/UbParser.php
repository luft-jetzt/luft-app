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

            try {
                $dateTime = \DateTime::createFromFormat(self::DATETIME_FORMAT, $parts[self::DATETIME]);
            } catch (\Exception $e) {

            }

            $value = new Value();

            try {
                $value
                    ->setStation($parts[self::STATION])
                    ->setDateTime($dateTime)
                    ->setValue($parts[self::VALUE])
                ;
            } catch (\Exception $e) {

            }

            $valueList[] = $value;
        }

        return $valueList;
    }
}