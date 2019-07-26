<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColors;

class StandardLevelColors extends AbstractLevelColors
{
    protected $backgroundColors = [
        0 => 'white',
        1 => '#28a745',
        2 => '#28a745',
        3 => '#ffc107',
        4 => '#ffc107',
        5 => '#dc3545',
        6 => '#dc3545',
    ];

    protected $backgroundColorNames = [
        0 => 'white',
        1 => 'green',
        2 => 'green',
        3 => 'yellow',
        4 => 'yellow',
        5 => 'red',
        6 => 'red',
    ];
}