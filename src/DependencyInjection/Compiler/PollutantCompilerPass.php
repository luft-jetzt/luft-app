<?php declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Air\MeasurementList\MeasurementListInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class PollutantCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(MeasurementListInterface::class)) {
            return;
        }

        $measurementList = $container->findDefinition(MeasurementListInterface::class);

        $taggedServices = $container->findTaggedServiceIds('measurement');

        foreach ($taggedServices as $id => $tags) {
            $measurementList->addMethodCall('addMeasurement', [new Reference($id)]);
        }
    }
}
