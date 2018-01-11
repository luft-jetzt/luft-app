<?php declare(strict_types=1);

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

    public function parseData(): array
    {
        $zipEntityList = [];

        $line = array_shift($this->lines); //throw headline columns away

        $parts = explode("\t", $line);

        if (count($parts) == 16 && $parts[self::FIELD_ZIP]) {
            $latitude = (float) $parts[self::FIELD_LATITUDE];
            $longitude = (float) $parts[self::FIELD_LONGITUDE];
            $zipCodes = explode(',', $parts[self::FIELD_ZIP]);

            foreach ($zipCodes as $zipCode) {
                $zipEntity = new Zip($latitude, $longitude);

                $zipEntity->setZip($zipCode);

                $zipEntityList[] = $zipEntity;
            }
        }

        return $zipEntityList;
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
