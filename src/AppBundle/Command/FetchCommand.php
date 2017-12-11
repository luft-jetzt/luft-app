<?php

namespace AppBundle\Command;

use AppBundle\Pollution\Pollutant\PollutantInterface;
use AppBundle\SourceFetcher\Parser\UbParser;
use AppBundle\SourceFetcher\Persister\Persister;
use AppBundle\SourceFetcher\Query\AbstractQuery;
use AppBundle\SourceFetcher\Query\QueryInterface;
use AppBundle\SourceFetcher\Query\UbCOQuery;
use AppBundle\SourceFetcher\Query\UbNO2Query;
use AppBundle\SourceFetcher\Query\UbO3Query;
use AppBundle\SourceFetcher\Query\UbPM10Query;
use AppBundle\SourceFetcher\Query\UbSO2Query;
use AppBundle\SourceFetcher\SourceFetcher;
use AppBundle\SourceFetcher\UbSourceFetcher;
use AppBundle\SourceFetcher\Value\Value;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
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
            $this->fetchPM10($output, $dateTime25);
        }

        if ($input->getOption('so2')) {
            $this->fetchSO2($output, $dateTime4);
        }

        if ($input->getOption('no2')) {
            $this->fetchNO2($output, $dateTime4);
        }

        if ($input->getOption('o3')) {
            $this->fetchO3($output, $dateTime4);
        }

        if ($input->getOption('co')) {
            $this->fetchCO($output, $dateTime4);
        }
    }

    protected function fetchPM10(OutputInterface $output, \DateTimeInterface $dateTime)
    {
        $output->writeln('PM10');

        $query = new UbPM10Query($dateTime);

        $this->fetch($output, $query, PollutantInterface::POLLUTANT_PM10);
    }

    protected function fetchSO2(OutputInterface $output, \DateTimeInterface $dateTime)
    {
        $output->writeln('SO2');

        $query = new UbSO2Query($dateTime);

        $this->fetch($output, $query, PollutantInterface::POLLUTANT_SO2);
    }

    protected function fetchNO2(OutputInterface $output, \DateTimeInterface $dateTime)
    {
        $output->writeln('NO2');

        $query = new UbNO2Query($dateTime);

        $this->fetch($output, $query, PollutantInterface::POLLUTANT_NO2);
    }

    protected function fetchO3(OutputInterface $output, \DateTimeInterface $dateTime)
    {
        $output->writeln('O3');

        $query = new UbO3Query($dateTime);

        $this->fetch($output, $query, PollutantInterface::POLLUTANT_O3);
    }

    protected function fetchCO(OutputInterface $output, \DateTimeInterface $dateTime)
    {
        $output->writeln('CO');

        $query = new UbCOQuery($dateTime);

        $this->fetch($output, $query, PollutantInterface::POLLUTANT_CO);
    }

    protected function fetch(OutputInterface $output, QueryInterface $query, string $pollutant)
    {
        $sourceFetcher = new UbSourceFetcher();

        $response = $sourceFetcher->query($query);

        $parser = new UbParser($query);
        $tmpValueList = $parser->parse($response, $pollutant);

        $persister = $this->getContainer()->get(Persister::class);
        $persister->persistValues($tmpValueList);

        $this->writeValueTable($output, $persister->getNewValueList());
    }

    protected function writeValueTable(OutputInterface $output, array $newValueList): void
    {
        $table = new Table($output);
        $table->setHeaders(['Station', 'Title', 'Value', 'DateTime']);

        /** @var Value $value */
        foreach ($newValueList as $value) {
            $table->addRow([$value->getStation()->getStationCode(), $value->getStation()->getTitle(), $value->getValue(), $value->getDateTime()->format('Y-m-d H:i:s')]);
        }

        $table->render();
    }
}
