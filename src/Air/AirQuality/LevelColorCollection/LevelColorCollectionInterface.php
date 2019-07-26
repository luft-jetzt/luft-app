<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColorCollection;

use App\Air\AirQuality\LevelColors\LevelColorsInterface;

interface LevelColorCollectionInterface
{
    public function getLevelColorsList(): array;
    public function getLevelColorsForMeasurement(string $measurementIdentifier): LevelColorsInterface;
}
