<?php declare(strict_types=1);

namespace App\CityLoader;

use App\Entity\Zip;

class ZipLoader
{
    const FIELD_LATITUDE = 4;
    const FIELD_LONGITUDE = 5;
    const FIELD_ZIP = 7;

    /** @var array $lines */
    protected $lines = [];

    /** @var CsvLoaderInterface $csvLoader */
    protected $csvLoader;

    public function __construct(CsvLoaderInterface $csvLoader)
    {
        $this->csvLoader = $csvLoader;
    }

    public function loadData(): ZipLoader
    {
        $this->lines = $this->csvLoader->loadLines();

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

    public function hasData(): bool
    {
        return count($this->lines) > 0;
    }

    public function countData(): int
    {
        return count($this->lines);
    }
}
