<?php declare(strict_types=1);

namespace App\Menu;

use App\Pollution\Pollutant\PollutantInterface;
use App\Pollution\PollutantList\PollutantListInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MainMenuBuilder extends AbstractBuilder
{
    /** @var PollutantListInterface $pollutantList */
    protected $pollutantList;

    /** @var RouterInterface $router */
    protected $router;

    public function __construct(FactoryInterface $factory, TokenStorageInterface $tokenStorage, PollutantListInterface $pollutantList, RouterInterface $router)
    {
        $this->pollutantList = $pollutantList;
        $this->router = $router;

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

        $this->buildPollutantMenuItems($pollutantDropdown);

        $pollutantDropdown->addChild('Grenzwerte', ['route' => 'limits', 'attributes' => ['divider_prepend' => true]]);
        $pollutantDropdown->addChild('Fahrverbote', ['uri' => 'https://sqi.be/i7vfr']);

        $analysisDropdown = $menu->addChild('Analyse', [
            'attributes' => [
                'dropdown' => true,
            ],
        ]);

        $analysisDropdown->addChild('Komfortofen-Finder', ['route' => 'analysis_komfortofen']);

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

    protected function buildPollutantMenuItems(ItemInterface $pollutantDropdown): ItemInterface
    {
        $pollutants = $this->pollutantList->getPollutants();

        usort($pollutants, function(PollutantInterface $a, PollutantInterface $b): int {
            if ($a->getName() === $b->getName()) {
                return 0;
            }

            return ($a->getName() < $b->getName()) ? -1 : 1;
        });

        /** @var PollutantInterface $pollutant */
        foreach ($pollutants as $pollutant) {
            $routeName = sprintf('pollutant_%s', strtolower($pollutant->getIdentifier()));

            if ($this->router->getRouteCollection()->get($routeName)) {
                $label = sprintf('%s (%s)', $pollutant->getName(), $pollutant->getShortNameHtml());

                $pollutantDropdown->addChild($label, ['route' => $routeName, 'attributes' => ['title' => sprintf('Erfahre mehr über %s', $label)]]);
            }
        }

        return $pollutantDropdown;
    }
}
