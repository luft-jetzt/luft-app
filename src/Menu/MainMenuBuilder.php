<?php declare(strict_types=1);

namespace App\Menu;

use App\Air\Measurement\MeasurementInterface;
use App\Air\MeasurementList\MeasurementListInterface;
use Flagception\Manager\FeatureManagerInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MainMenuBuilder extends AbstractBuilder
{
    /** @var MeasurementListInterface $measurementList */
    protected $measurementList;

    /** @var RouterInterface $router */
    protected $router;

    /** @var FeatureManagerInterface $featureManager */
    protected $featureManager;

    public function __construct(FeatureManagerInterface $featureManager, FactoryInterface $factory, TokenStorageInterface $tokenStorage, MeasurementListInterface $measurementList, RouterInterface $router)
    {
        $this->measurementList = $measurementList;
        $this->router = $router;
        $this->featureManager = $featureManager;

        parent::__construct($factory, $tokenStorage);
    }

    public function mainMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('class', 'nav navbar-nav mr-auto');

        $pollutantDropdown = $menu->addChild('Schadstoffe', [
            'attributes' => [
                'dropdown' => true,
            ],
        ]);

        $this->addMeasurementDropdown($pollutantDropdown);

        $pollutantDropdown->addChild('Grenzwerte', ['route' => 'limits', 'attributes' => ['divider_prepend' => true]]);
        $pollutantDropdown->addChild('Fahrverbote', ['uri' => 'https://sqi.be/i7vfr']);

        if ($this->featureManager->isActive('analysis')) {
            $analysisDropdown = $menu->addChild('Analyse', [
                'attributes' => [
                    'dropdown' => true,
                ],
            ]);

            if ($this->featureManager->isActive('analysis_komfortofen')) {
                $analysisDropdown->addChild('Komfortofen-Finder <sup>beta</sup>', ['route' => 'analysis_komfortofen']);
            }

            if ($this->featureManager->isActive('analysis_fireworks')) {
                $analysisDropdown->addChild('Silvester-Feuerwerk <sup>beta</sup>', ['route' => 'analysis_fireworks']);
                $analysisDropdown->addChild('Corona-Feuerwerk <sup>beta</sup>', ['route' => 'analysis_fireworks_corona']);
            }
        }

        $aboutDropdown = $menu->addChild('Über', [
            'attributes' => [
                'dropdown' => true,
            ],
        ]);

        $aboutDropdown->addChild('Impressum', ['route' => 'impress']);
        $aboutDropdown->addChild('Datenschutz', ['route' => 'privacy']);

        $pollutantDropdown = $menu->addChild('Api', [
            'attributes' => [
                'dropdown' => true,
            ],
        ]);

        $pollutantDropdown->addChild('Api-Dokumentation', ['route' => 'app.swagger_ui']);
        $pollutantDropdown->addChild('GitHub-Repository', ['uri' => 'https://sqi.be/ed94a']);

        return $menu;
    }

    protected function addMeasurementDropdown(ItemInterface $measurementDropdown): ItemInterface
    {
        $measurements = $this->measurementList->getMeasurements();

        usort($measurements, function(MeasurementInterface $a, MeasurementInterface $b): int {
            if ($a->getName() === $b->getName()) {
                return 0;
            }

            return ($a->getName() < $b->getName()) ? -1 : 1;
        });

        /** @var MeasurementInterface $measurement */
        foreach ($measurements as $measurement) {
            $routeName = sprintf('pollutant_%s', strtolower($measurement->getIdentifier()));

            if ($this->router->getRouteCollection()->get($routeName)) {
                $label = sprintf('%s (%s)', $measurement->getName(), $measurement->getShortNameHtml());

                $measurementDropdown->addChild($label, ['route' => $routeName, 'attributes' => ['title' => sprintf('Erfahre mehr über %s', $label)]]);
            }
        }

        return $measurementDropdown;
    }
}
