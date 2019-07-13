<?php declare(strict_types=1);

namespace App\Twig\Extension;

class TypeTwigExtension extends \Twig_Extension
{
    public function getTests(): array
    {
        return [
            new \Twig_Test('numeric', function ($value) { return  is_numeric($value); }),
        ];
    }

    public function getName(): string
    {
        return 'type_extension';
    }
}

