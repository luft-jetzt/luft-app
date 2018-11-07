<?php declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\AirQuality\Calculator\AirQualityCalculatorInterface;
use App\Command\StationCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ProviderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(StationCommand::class)) {
            return;
        }

        $stationCommand = $container->findDefinition(StationCommand::class);

        $taggedServices = $container->findTaggedServiceIds('air_provider');

        foreach ($taggedServices as $id => $tags) {
            $stationCommand->addMethodCall('addProvider', [new Reference($id)]);
        }
    }
}
