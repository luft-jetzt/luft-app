<?php declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\ItemInterface;

class MapMenuBuilder extends MainMenuBuilder
{
    public function mapMenu(array $options = []): ItemInterface
    {
        $mainMenu = parent::mainMenu($options);

        $mainMenu->addChild('Sidebar', ['uri' => '#', 'attributes' => ['class' => 'nav-link', 'id' => 'list-btn']]);

        return $mainMenu;
    }
}
