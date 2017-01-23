<?php

namespace AppBundle\Command;

use AppBundle\SourceFetcher\Parser\UbParser;
use AppBundle\SourceFetcher\Query\UbCOQuery;
use AppBundle\SourceFetcher\Query\UbNO2Query;
use AppBundle\SourceFetcher\Query\UbO3Query;
use AppBundle\SourceFetcher\Query\UbPm10Query;
use AppBundle\SourceFetcher\Query\UbSO2Query;
use AppBundle\SourceFetcher\SourceFetcher;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('luft:fetch')
            ->setDescription('')
            ->addOption('pm10')
            ->addOption('so2')
            ->addOption('no2')
            ->addOption('o3')
            ->addOption('co')
            ->addArgument('dateTime', InputArgument::OPTIONAL);
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('dateTime')) {
            $dateTime = new \DateTime($input->getArgument('dateTime'));
        } else {
            $dateTime = new \DateTime('2017-01-15 12:00:00');
        }

        if ($input->getOption('pm10')) {
            $this->fetchPM10($dateTime);
        }

        if ($input->getOption('so2')) {
            $this->fetchSO2($dateTime);
        }

        if ($input->getOption('no2')) {
            $this->fetchNO2($dateTime);
        }

        if ($input->getOption('o3')) {
            $this->fetchO3($dateTime);
        }

        if ($input->getOption('co')) {
            $this->fetchCO($dateTime);
        }

    }

    protected function fetchPM10(\DateTime $dateTime)
    {
        $query = new UbPM10Query($dateTime);

        $sourceFetcher = new SourceFetcher();
        $sourceFetcher->query($query);
    }

    protected function fetchSO2(\DateTime $dateTime)
    {
        $query = new UbSO2Query($dateTime);

        $sourceFetcher = new SourceFetcher();
        $sourceFetcher->query($query);
    }

    protected function fetchNO2(\DateTime $dateTime)
    {
        $query = new UbNO2Query($dateTime);

        $sourceFetcher = new SourceFetcher();
        $sourceFetcher->query($query);
    }

    protected function fetchO3(\DateTime $dateTime)
    {
        $query = new UbO3Query($dateTime);

        $sourceFetcher = new SourceFetcher();

        $response = $sourceFetcher->query($query);

        $parser = new UbParser();
        $tmpValueList = $parser->parse($response);

        var_dump($tmpValueList);
    }

    protected function fetchCO(\DateTime $dateTime)
    {
        $query = new UbCOQuery($dateTime);

        $sourceFetcher = new SourceFetcher();
        $sourceFetcher->query($query);
    }
}