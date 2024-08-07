<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColors;

class UVIndexLevelColors extends AbstractLevelColors
{
    protected array $backgroundColors = [
        1 => '#28a745',
        2 => '#ffc107',
        3 => '#f87820',
        4 => '#dc3545',
        5 => '#a80080',
    ];

    protected array $backgroundColorNames = [
        1 => 'green',
        2 => 'yellow',
        3 => 'orange',
        4 => 'red',
        5 => 'purple',
    ];

    #[\Override]
    public function getIdentifier(): string
    {
        return 'uvindex';
    }
}
