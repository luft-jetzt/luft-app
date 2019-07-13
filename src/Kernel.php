<?php declare(strict_types=1);

namespace App;

use App\DependencyInjection\Compiler\TwigSeoExtensionPass;
use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;
use App\Air\Measurement\MeasurementInterface;
use App\DependencyInjection\Compiler\PollutionLevelCompilerPass;
use App\DependencyInjection\Compiler\ProviderCompilerPass;
use App\Provider\ProviderInterface;
use App\DependencyInjection\Compiler\PollutantCompilerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getCacheDir()
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return $this->getProjectDir().'/var/log';
    }

    public function registerBundles()
    {
        $contents = require $this->getProjectDir().'/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        // Feel free to remove the "container.autowiring.strict_mode" parameter
        // if you are using symfony/dependency-injection 4.0+ as it's the default behavior
        $container->setParameter('container.autowiring.strict_mode', true);
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir().'/config';

        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{packages}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/services/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}_'.$this->environment.self::CONFIG_EXTS, 'glob');

        $container->addCompilerPass(new TwigSeoExtensionPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);

        $container->addCompilerPass(new PollutionLevelCompilerPass());
        $container->registerForAutoconfiguration(PollutionLevelInterface::class)->addTag('pollution_level');

        $container->addCompilerPass(new ProviderCompilerPass());
        $container->registerForAutoconfiguration(ProviderInterface::class)->addTag('air_provider');

        $container->addCompilerPass(new PollutantCompilerPass());
        $container->registerForAutoconfiguration(MeasurementInterface::class)->addTag('measurement');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $confDir = $this->getProjectDir().'/config';

        $routes->import($confDir.'/{routes}/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}'.self::CONFIG_EXTS, '/', 'glob');
    }
}
