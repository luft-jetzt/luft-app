<?php declare(strict_types=1);

namespace App\Provider\Luftdaten\StationLoader;

use App\Entity\Station;
use App\Provider\AbstractStationLoader;
use App\Provider\StationLoaderInterface;
use Curl\Curl;
use Doctrine\ORM\EntityManager;

class LuftdatenStationLoader extends AbstractStationLoader
{
    const SOURCE_URL = 'https://api.luftdaten.info/static/v2/data.dust.min.json';

    /** @var \stdClass $sensorData */
    protected $sensorData;

    public function process(callable $callback): StationLoaderInterface
    {
        if (!$this->sensorData) {
            return $this;
        }

        /** @var EntityManager $em */
        $em = $this->registry->getManager();

        foreach ($this->sensorData as $data) {
            $callback();

            $station = $this->createStation($data->location);

            if (!$this->stationExists($station->getStationCode())) {

                $em->persist($station);

                $this->newStationList[$station->getStationCode()] = $station;
            }
        }

        $em->flush();

        return $this;
    }

    public function load(): StationLoaderInterface
    {
        $this->existingStationList = $this->getExistingStationList('ld');

        $curl = new Curl();
        $curl->get(self::SOURCE_URL);

        $this->sensorData = $curl->response;

        return $this;
    }

    public function count(): int
    {
        if (!$this->sensorData) {
            return 0;
        }

        return 1;
    }

    public function setUpdate(bool $update = false): StationLoaderInterface
    {
        // TODO: Implement setUpdate() method.
    }

    protected function createStation(\stdClass $locationData): Station
    {
        $stationCode = sprintf('LFTDTN%d', $locationData->id);

        $station = new Station((float) $locationData->latitude, (float) $locationData->longitude);

        $station
            ->setAltitude((int) $locationData->altitude)
            ->setStationCode($stationCode)
            ->setProvider('ld');

        return $station;
    }
}
