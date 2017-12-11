<?php

namespace AppBundle\Command;

use AppBundle\Pollution\Pollutant\PollutantInterface;
use AppBundle\SourceFetcher\LdSourceFetcher;
use AppBundle\SourceFetcher\Parser\UbParser;
use AppBundle\SourceFetcher\Persister\Persister;
use AppBundle\SourceFetcher\Query\LdQuery\LdPM10Query;
use AppBundle\SourceFetcher\Query\LdQuery\LdPM25Query;
use AppBundle\SourceFetcher\Query\QueryInterface;
use AppBundle\SourceFetcher\Query\UbQuery\UbCOQuery;
use AppBundle\SourceFetcher\Query\UbQuery\UbNO2Query;
use AppBundle\SourceFetcher\Query\UbQuery\UbO3Query;
use AppBundle\SourceFetcher\Query\UbQuery\UbPM10Query;
use AppBundle\SourceFetcher\Query\UbQuery\UbSO2Query;
use AppBundle\SourceFetcher\UbSourceFetcher;
use AppBundle\SourceFetcher\Value\Value;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Fetch2Command extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('luft:fetch2')
            ->setDescription('')
            ->addOption('pm10')
            ->addOption('pm25')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dateTime = new \DateTimeImmutable();

        if ($input->getOption('pm10')) {
            $this->fetchPM10($output);
        }

        if ($input->getOption('pm25')) {
            $this->fetchPM25($output);
        }
    }

    protected function fetchPM10(OutputInterface $output)
    {
        $output->writeln('PM10');

        $this->fetch($output, new LdPM10Query(), PollutantInterface::POLLUTANT_PM10);
    }

    protected function fetchPM25(OutputInterface $output)
    {
        $output->writeln('PM25');

        $this->fetch($output, new LdPM25Query(), PollutantInterface::POLLUTANT_SO2);
    }

    protected function fetch(OutputInterface $output, QueryInterface $query, string $pollutant)
    {
        $sourceFetcher = new LdSourceFetcher();

        $response = $sourceFetcher->query($query);

        $parser = new LdParser($query);
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
