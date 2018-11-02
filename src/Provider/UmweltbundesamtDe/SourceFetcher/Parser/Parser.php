<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Parser;

use App\Provider\UmweltbundesamtDe\Query\QueryInterface;

class Parser implements ParserInterface
{
    const STATION = 0;
    const DATETIME = 5;
    const VALUE = 6;

    protected $query = null;

    public function __construct(QueryInterface $query)
    {
        $this->query = $query;
    }

    public function parse(string $string, int $pollutant): array
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
                $value = (float) $parts[self::VALUE];

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
