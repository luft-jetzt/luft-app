<?php declare(strict_types=1);

namespace App\Pollution\StationCache;

use App\Entity\Station;

interface StationCacheInterface
{
    public function getList(): array;
    public function getStationByCode(string $stationCode): ?Station;
    public function stationExists(string $stationCode): bool;
    public function getStationReferenceByCode(string $stationCode): ?Station;
}
