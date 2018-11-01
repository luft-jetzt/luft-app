<?php declare(strict_types=1);

namespace App\Provider\Umweltbundesamt;

use App\Provider\AbstractProvider;
use App\Provider\Umweltbundesamt\StationLoader\UmweltbundesamtStationLoader;

class UmweltbundesamtProvider extends AbstractProvider
{
    public function __construct(UmweltbundesamtStationLoader $umweltbundesamtStationLoader)
    {
        $this->stationLoader = $umweltbundesamtStationLoader;
    }

    public function getIdentifier(): string
    {
        return 'uba_de';
    }
}
