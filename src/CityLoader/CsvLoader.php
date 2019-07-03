<?php declare(strict_types=1);

namespace App\CityLoader;

class CsvLoader implements CsvLoaderInterface
{
    public function __construct()
    {

    }

    public function loadLines(): array
    {
        $lines = file(self::SOURCE_URL);

        array_shift($lines);

        return $lines;
    }
}