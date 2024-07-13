<?php declare(strict_types=1);

namespace App;

use App\Air\AirQuality\LevelColors\LevelColorsInterface;
use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;
use App\Air\Measurement\MeasurementInterface;
use App\Air\Provider\ProviderInterface;
use App\DependencyInjection\Compiler\LevelColorCompilerPass;
use App\DependencyInjection\Compiler\PollutantCompilerPass;
use App\DependencyInjection\Compiler\PollutionLevelCompilerPass;
use App\DependencyInjection\Compiler\ProviderCompilerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
    final public const string CONFIG_EXTS = '.{php,xml,yaml,yml}';

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->setParameter('container.autowiring.strict_mode', true);
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir().'/config';

        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{packages}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}_'.$this->environment.self::CONFIG_EXTS, 'glob');

        $container->addCompilerPass(new PollutionLevelCompilerPass());
        $container->registerForAutoconfiguration(PollutionLevelInterface::class)->addTag('pollution_level');

        $container->addCompilerPass(new ProviderCompilerPass());
        $container->registerForAutoconfiguration(ProviderInterface::class)->addTag('air_provider');

        $container->addCompilerPass(new PollutantCompilerPass());
        $container->registerForAutoconfiguration(MeasurementInterface::class)->addTag('measurement');

        $container->addCompilerPass(new LevelColorCompilerPass());
        $container->registerForAutoconfiguration(LevelColorsInterface::class)->addTag('level_colors');
    }
}
