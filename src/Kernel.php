<?php declare(strict_types=1);

namespace App;

use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;
use App\Air\Measurement\MeasurementInterface;
use App\DependencyInjection\Compiler\PollutantCompilerPass;
use App\DependencyInjection\Compiler\PollutionLevelCompilerPass;
use App\DependencyInjection\Compiler\ProviderCompilerPass;
use App\Provider\ProviderInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->addCompilerPass(new PollutionLevelCompilerPass());
        $container->registerForAutoconfiguration(PollutionLevelInterface::class)->addTag('pollution_level');

        $container->addCompilerPass(new ProviderCompilerPass());
        $container->registerForAutoconfiguration(ProviderInterface::class)->addTag('air_provider');

        $container->addCompilerPass(new PollutantCompilerPass());
        $container->registerForAutoconfiguration(MeasurementInterface::class)->addTag('measurement');
    }
}
