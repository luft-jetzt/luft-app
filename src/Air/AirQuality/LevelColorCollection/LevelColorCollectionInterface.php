<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColorCollection;

interface LevelColorCollectionInterface
{
    public function getBackgroundColor(int $pollutionLevel): string;
    public function getBackgroundColorName(int $pollutionLevel): string;
}
