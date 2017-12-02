<?php

namespace AppBundle\Command;

use AppBundle\Pollution\Pollutant\PollutantInterface;
use AppBundle\SourceFetcher\Parser\UbParser;
use AppBundle\SourceFetcher\Persister\Persister;
use AppBundle\SourceFetcher\Query\AbstractQuery;
use AppBundle\SourceFetcher\Query\UbCOQuery;
use AppBundle\SourceFetcher\Query\UbNO2Query;
use AppBundle\SourceFetcher\Query\UbO3Query;
use AppBundle\SourceFetcher\Query\UbPM10Query;
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
            $dateTime = new \DateTimeImmutable($input->getArgument('dateTime'));
        } else {
            $dateTime = new \DateTimeImmutable();
        }

        $dateInterval2 = new \DateInterval('PT2H');
        $dateInterval4 = new \DateInterval('PT4H');
        $dateInterval8 = new \DateInterval('PT8H');
        $dateInterval25 = new \DateInterval('PT25H');

        $dateTime2 = $dateTime->sub($dateInterval2);
        $dateTime4 = $dateTime->sub($dateInterval4);
        $dateTime8 = $dateTime->sub($dateInterval8);
        $dateTime25 = $dateTime->sub($dateInterval25);

        if ($input->getOption('pm10')) {
            $this->fetchPM10($dateTime25);
        }

        if ($input->getOption('so2')) {
            $this->fetchSO2($dateTime4);
        }

        if ($input->getOption('no2')) {
            $this->fetchNO2($dateTime4);
        }

        if ($input->getOption('o3')) {
            $this->fetchO3($dateTime4);
        }

        if ($input->getOption('co')) {
            $this->fetchCO($dateTime4);
        }
    }

    protected function fetchPM10(\DateTimeInterface $dateTime)
    {
        $query = new UbPM10Query($dateTime);

        $this->fetch($query, PollutantInterface::POLLUTANT_PM10);
    }

    protected function fetchSO2(\DateTimeInterface $dateTime)
    {
        $query = new UbSO2Query($dateTime);

        $this->fetch($query, PollutantInterface::POLLUTANT_SO2);
    }

    protected function fetchNO2(\DateTimeInterface $dateTime)
    {
        $query = new UbNO2Query($dateTime);

        $this->fetch($query, PollutantInterface::POLLUTANT_NO2);
    }

    protected function fetchO3(\DateTimeInterface $dateTime)
    {
        $query = new UbO3Query($dateTime);

        $this->fetch($query, PollutantInterface::POLLUTANT_O3);
    }

    protected function fetchCO(\DateTimeInterface $dateTime)
    {
        $query = new UbCOQuery($dateTime);

        $this->fetch($query, PollutantInterface::POLLUTANT_CO);
    }

    protected function fetch(AbstractQuery $query, string $pollutant)
    {
        $sourceFetcher = new SourceFetcher();

        $response = $sourceFetcher->query($query);

        $parser = new UbParser($query);
        $tmpValueList = $parser->parse($response, $pollutant);

        $persister = $this->getContainer()->get('AppBundle\SourceFetcher\Persister\Persister');
        $persister->persistValues($tmpValueList);
    }
}
