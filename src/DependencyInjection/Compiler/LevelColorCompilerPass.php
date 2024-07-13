<?php declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Air\AirQuality\LevelColorCollection\LevelColorCollectionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class LevelColorCompilerPass implements CompilerPassInterface
{
    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(LevelColorCollectionInterface::class)) {
            return;
        }

        $levelColorCollection = $container->findDefinition(LevelColorCollectionInterface::class);

        $taggedServices = $container->findTaggedServiceIds('level_colors');

        foreach ($taggedServices as $id => $tags) {
            $levelColorCollection->addMethodCall('addLevelColors', [new Reference($id)]);
        }
    }
}
