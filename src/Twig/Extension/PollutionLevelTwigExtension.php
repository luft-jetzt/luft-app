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

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('pollution_color', [$this, 'pollutionColor'], ['is_safe' => ['raw']]),
        ];
    }

    public function pollutionColor(int $pollutionLevel): string
    {
        return $this->backgroundColors[$pollutionLevel];
    }

    public function getName(): string
    {
        return 'pollution_level_extension';
    }
}

