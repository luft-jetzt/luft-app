<?php declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Air\Provider\ProviderListInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ProviderCompilerPass implements CompilerPassInterface
{
    #[\Override]
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(ProviderListInterface::class)) {
            return;
        }

        $stationCommand = $container->findDefinition(ProviderListInterface::class);

        $taggedServices = $container->findTaggedServiceIds('air_provider');

        foreach ($taggedServices as $id => $tags) {
            $stationCommand->addMethodCall('addProvider', [new Reference($id)]);
        }
    }
}
