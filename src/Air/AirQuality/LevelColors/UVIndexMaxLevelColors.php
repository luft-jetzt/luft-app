<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColors;

class UVIndexMaxLevelColors extends AbstractLevelColors
{
    protected array $backgroundColors = [
        1 => '#28a745',
        2 => '#ffc107',
        3 => '#f87820',
        5 => '#dc3545',
        4 => '#a80080',
    ];

    protected array $backgroundColorNames = [
        1 => 'green',
        2 => 'yellow',
        3 => 'orange',
        4 => 'red',
        5 => 'purple',
    ];
}
