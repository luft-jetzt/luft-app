<?php declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Air\PollutantList\PollutantListInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class PollutantCompilerPass implements CompilerPassInterface
{
    #[\Override]
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(PollutantListInterface::class)) {
            return;
        }

        $measurementList = $container->findDefinition(PollutantListInterface::class);

        $taggedServices = $container->findTaggedServiceIds('measurement');

        foreach ($taggedServices as $id => $tags) {
            $measurementList->addMethodCall('addMeasurement', [new Reference($id)]);
        }
    }
}
