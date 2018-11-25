<?php declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\ItemInterface;

class MainMenuBuilder extends AbstractBuilder
{
    public function mainMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        $pollutantDropdown = $menu->addChild('Schadstoffe', [
            'attributes' => [
                'dropdown' => true,
            ],
        ]);

        $pollutantDropdown->addChild('Feinstaub PM<sub>10</sub>', ['route' => 'pollutant_pm10']);
        $pollutantDropdown->addChild('Stickstoffdioxid NO<sub>2</sub>', ['route' => 'pollutant_no2']);
        $pollutantDropdown->addChild('Schwefeldioxid SO<sub>2</sub>', ['route' => 'pollutant_so2']);
        $pollutantDropdown->addChild('Kohlenmonoxid CO', ['route' => 'pollutant_co']);
        $pollutantDropdown->addChild('Ozon O<sub>3</sub>', ['route' => 'pollutant_o3', 'attributes' => ['divider_append' => true,],]);
        $pollutantDropdown->addChild('Grenzwerte', ['route' => 'limits']);
        $pollutantDropdown->addChild('Fahrverbote', ['uri' => 'https://sqi.be/i7vfr']);

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
}
