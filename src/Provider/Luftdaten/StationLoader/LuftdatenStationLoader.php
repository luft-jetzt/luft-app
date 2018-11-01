<?php declare(strict_types=1);

namespace App\Provider\Luftdaten\StationLoader;

use App\Entity\Station;
use App\Provider\AbstractStationLoader;
use App\Provider\StationLoaderInterface;
use Doctrine\ORM\EntityManager;

class LuftdatenStationLoader extends AbstractStationLoader
{
    const SOURCE_URL = 'https://www.env-it.de/stationen/public/download.do?event=euMetaStation';

    public function process(callable $callback): StationLoaderInterface
    {
        return $this;
    }

    public function load(): StationLoaderInterface
    {
        // TODO: Implement load() method.
    }

    public function count(): int
    {
        // TODO: Implement count() method.
    }

    public function setUpdate(bool $update = false): StationLoaderInterface
    {
        // TODO: Implement setUpdate() method.
    }

    public function getExistingStationList(): array
    {
        // TODO: Implement getExistingStationList() method.
    }

    public function getNewStationList(): array
    {
        // TODO: Implement getNewStationList() method.
    }
}
