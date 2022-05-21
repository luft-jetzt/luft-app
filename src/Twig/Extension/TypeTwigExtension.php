<?php declare(strict_types=1);

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

class TypeTwigExtension extends AbstractExtension
{
    public function getTests(): array
    {
        return [
            new TwigTest('numeric', function ($value) { return  is_numeric($value); }),
        ];
    }

    public function getName(): string
    {
        return 'type_extension';
    }
}

