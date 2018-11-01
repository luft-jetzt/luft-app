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
}
