<?php

namespace AppBundle\Command;

use AppBundle\Entity\Data;
use AppBundle\Entity\Station;
use AppBundle\SourceFetcher\Parser\UbParser;
use AppBundle\SourceFetcher\Persister\Persister;
use AppBundle\SourceFetcher\Query\AbstractQuery;
use AppBundle\SourceFetcher\Query\UbCOQuery;
use AppBundle\SourceFetcher\Query\UbNO2Query;
use AppBundle\SourceFetcher\Query\UbO3Query;
use AppBundle\SourceFetcher\Query\UbPM10Query;
use AppBundle\SourceFetcher\Query\UbSO2Query;
use AppBundle\SourceFetcher\SourceFetcher;
use Curl\Curl;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('luft:station')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $existingStationList = $this->getExistingStations();
        $newStationData = $this->fetchStationList();

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();

        foreach ($newStationData as $stationData) {
            $station = $this->createStation($stationData);

            $em->merge($station);
        }

        $em->flush();
    }

    protected function getExistingStations(): array
    {
        $stations = $this->getContainer()->get('doctrine')->getRepository('AppBundle:Station')->findAll();

        $stationList = [];

        /** @var Station $station */
        foreach ($stations as $station) {
            $stationList[$station->getStationCode()] = $station;
        }

        return $stationList;
    }

    protected function fetchStationList(): array
    {
        $curl = new Curl();
        $curl->get('https://www.umweltbundesamt.de/js/uaq/data/stations/limits');

        $limitData = json_decode($curl->response);
        $stationList = $limitData->stations_idx;

        return $stationList;
    }

    public function mergeStation(Station $station, $stationData): Station
    {
        $station
            ->setTitle($stationData[1])
            ->setStationCode($stationData[0])
            ->setStateCode($stationData[2])
            ->setLatitude($stationData[5])
            ->setLongitude($stationData[4]);

        return $station;
    }

    protected function createStation(array $stationData): Station
    {
        $station = new Station();

        $this->mergeStation($station, $stationData);

        return $station;
    }

    protected function stationExists(string $stationCode, array $stationData): bool
    {
        return array_key_exists($stationCode, $stationData);
    }
}
