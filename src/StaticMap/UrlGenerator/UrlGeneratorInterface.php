<?php declare(strict_types=1);

namespace App\StaticMap\UrlGenerator;

use App\StaticMap\StaticMapableInterface;

interface UrlGeneratorInterface
{
    public function generate(StaticMapableInterface $object, int $width = null, int $height = null, int $zoom = null): string;
}
