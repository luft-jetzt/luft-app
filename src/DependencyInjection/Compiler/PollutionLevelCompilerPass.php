<?php declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Air\AirQuality\Calculator\AirQualityCalculatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class PollutionLevelCompilerPass implements CompilerPassInterface
{
    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(AirQualityCalculatorInterface::class)) {
            return;
        }

        $airQualityCalculator = $container->findDefinition(AirQualityCalculatorInterface::class);

        $taggedServices = $container->findTaggedServiceIds('pollution_level');

        foreach ($taggedServices as $id => $tags) {
            $airQualityCalculator->addMethodCall('addPollutionLevel', [new Reference($id)]);
        }
    }
}
