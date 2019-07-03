<?php declare(strict_types=1);

namespace App\CityLoader;

interface CsvLoaderInterface
{
    const SOURCE_URL = 'http://www.fa-technik.adfc.de/code/opengeodb/DE.tab';

    public function loadLines(): array;
}