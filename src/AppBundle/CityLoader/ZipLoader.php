<?php

namespace AppBundle\CityLoader;

use AppBundle\Entity\Zip;

class ZipLoader
{
    const SOURCE_URL = 'http://www.fa-technik.adfc.de/code/opengeodb/DE.tab';
    const FIELD_LATITUDE = 4;
    const FIELD_LONGITUDE = 5;
    const FIELD_ZIP = 7;

    protected $lines = [];

    public function __construct()
    {

    }

    public function loadData(): ZipLoader
    {
        $this->lines = file(self::SOURCE_URL);
        array_shift($this->lines);

        return $this;
    }

    public function parseData(): ?Zip
    {
        $line = array_shift($this->lines); //throw headline columns away

        $parts = explode("\t", $line);

        if (count($parts) == 16 && $parts[self::FIELD_ZIP]) {
            $latitude = (float) $parts[self::FIELD_LATITUDE];
            $longitude = (float) $parts[self::FIELD_LONGITUDE];

            $zip = new Zip($latitude, $longitude);

            $zip->setZip($parts[self::FIELD_ZIP]);

            return $zip;
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
