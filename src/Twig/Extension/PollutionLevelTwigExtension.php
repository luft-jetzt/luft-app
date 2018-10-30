<?php declare(strict_types=1);

namespace App\Twig\Extension;

class PollutionLevelTwigExtension extends \Twig_Extension
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

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('pollution_color', [$this, 'pollutionColor'], ['is_safe' => ['raw']]),
            new \Twig_SimpleFunction('pollution_color_name', [$this, 'pollutionColorName'], ['is_safe' => ['raw']]),
        ];
    }

    public function pollutionColor(int $pollutionLevel): string
    {
        return $this->backgroundColors[$pollutionLevel];
    }

    public function pollutionColorName(int $pollutionLevel): string
    {
        return $this->backgroundColorNames[$pollutionLevel];
    }

    public function getName(): string
    {
        return 'pollution_level_extension';
    }
}

