<?php declare(strict_types=1);

namespace App\Air\Provider;

interface StationLoaderInterface
{
    public function load(): StationLoaderInterface;
    public function count(): int;
    public function setUpdate(bool $update = false): StationLoaderInterface;
    public function process(callable $callback): StationLoaderInterface;
    public function getExistingStationList(): array;
    public function getNewStationList(): array;
}
