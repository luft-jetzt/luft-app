<?php declare(strict_types=1);

namespace App;

use App\Air\AirQuality\LevelColors\LevelColorsInterface;
use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;
use App\Air\Pollutant\PollutantInterface;
use App\DependencyInjection\Compiler\LevelColorCompilerPass;
use App\DependencyInjection\Compiler\PollutantCompilerPass;
use App\DependencyInjection\Compiler\PollutionLevelCompilerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new PollutionLevelCompilerPass());
        $container->registerForAutoconfiguration(PollutionLevelInterface::class)->addTag('pollution_level');

        $container->addCompilerPass(new PollutantCompilerPass());
        $container->registerForAutoconfiguration(PollutantInterface::class)->addTag('pollutant');

        $container->addCompilerPass(new LevelColorCompilerPass());
        $container->registerForAutoconfiguration(LevelColorsInterface::class)->addTag('level_colors');
    }
}
