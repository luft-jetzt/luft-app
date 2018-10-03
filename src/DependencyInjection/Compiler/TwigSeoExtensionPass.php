<?php declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Twig\Extension\SeoTwigExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class TwigSeoExtensionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (false === $container->hasDefinition('sonata.seo.twig.extension')) {
            return;
        }

        $container->removeDefinition('sonata.seo.twig.extension');
    }
}
