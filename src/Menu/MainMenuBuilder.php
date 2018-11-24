<?php declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\ItemInterface;

class MainMenuBuilder extends AbstractBuilder
{
    public function mainMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        $menu
            ->addChild('Critical Mass', ['uri' => '#', 'class' => 'dropdown'])
            ->setExtra('dropdown', true);

        $menu['Critical Mass']
            ->addChild('Über die Critical Mass');

        $menu['Critical Mass']
            ->addChild('Häufig gestellte Fragen');

        $menu['Critical Mass']
            ->addChild('Hilfe');

        $menu['Critical Mass']
            ->addChild('Über criticalmass.in');

        return $menu;
    }
}
