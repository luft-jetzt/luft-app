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
            ->setDescription('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $curl = new Curl();
        $curl->get('https://www.umweltbundesamt.de/js/uaq/data/stations/limits');

        $limitData = json_decode($curl->response);
        $stationList = $limitData->stations_idx;

        $em = $this->getContainer()->get('doctrine')->getManager();

        foreach ($stationList as $stationData) {
            $station = $this->createStation($stationData);

            $em->persist($station);
        }

        $em->flush();
    }

    protected function createStation(array $stationData): Station
    {
        $station = new Station();

        $station
            ->setTitle($stationData[1])
            ->setStationCode($stationData[0])
            ->setStateCode($stationData[2])
            ->setLatitude($stationData[4])
            ->setLongitude($stationData[5])
        ;

        return $station;
    }
}
