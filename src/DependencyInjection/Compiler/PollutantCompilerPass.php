<?php declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Pollution\PollutantList\PollutantListInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class PollutantCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(PollutantListInterface::class)) {
            return;
        }

        $pollutantList = $container->findDefinition(PollutantListInterface::class);

        $taggedServices = $container->findTaggedServiceIds('pollutant');

        foreach ($taggedServices as $id => $tags) {
            $pollutantList->addMethodCall('addPollutant', [new Reference($id)]);
        }
    }
}
