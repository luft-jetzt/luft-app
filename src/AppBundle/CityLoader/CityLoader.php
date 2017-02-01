<?php

namespace AppBundle\CityLoader;

class CityLoader
{
    const SOURCE_URL = 'http://www.fa-technik.adfc.de/code/opengeodb/DE.tab';
    const FIELD_LOCID = 0;
    const FIELD_CITY = 3;
    const FIELD_LATITUDE = 4;
    const FIELD_LONGITUDE = 5;
    const FIELD_ZIP = 7;
    const FIELD_POPULATION = 9;
    const FIELD_AREA = 10;

    protected $lines = [];

    public function __construct()
    {

    }

    public function loadData(): CityLoader
    {
        $this->lines = file(self::SOURCE_URL);
        array_shift($this->lines);

        return $this;
    }

    public function parseData(): ?City
    {
        $line = array_shift($this->lines);

        $parts = explode("\t", $line);

        if (count($parts) == 16 && $parts[self::FIELD_ZIP]) {
            $city = new City();

            $city
                ->setLocId((int) $parts[self::FIELD_LOCID])
                ->setName($parts[self::FIELD_CITY])
                ->setLatitude((float) $parts[self::FIELD_LATITUDE])
                ->setLongitude((float) $parts[self::FIELD_LONGITUDE])
                ->setZip($parts[self::FIELD_ZIP])
                ->setPopulation((int) $parts[self::FIELD_POPULATION])
                ->setArea((int) $parts[self::FIELD_AREA])
            ;

            return $city;
        }

        return null;
    }

    public function hasData(): int
    {
        return count($this->lines) > 0;
    }

    public function countData(): int
    {
        return count($this->lines);
    }
}
