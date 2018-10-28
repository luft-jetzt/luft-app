<?php declare(strict_types=1);

namespace App\StationLoader;

interface StationLoaderInterface
{
    const SOURCE_URL = 'https://www.env-it.de/stationen/public/download.do?event=euMetaStation';

    public function load(): StationLoaderInterface;
    public function count(): int;
    public function setUpdate(bool $update = false): StationLoaderInterface;
    public function process(callable $callback): StationLoaderInterface;
    public function getExistingStationList(): array;
    public function getNewStationList(): array;
}
