<?php declare(strict_types=1);

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
use AppBundle\SourceFetcher\Reporting\Ub1SMW;
use AppBundle\SourceFetcher\Reporting\Ub1TMW;
use AppBundle\SourceFetcher\Reporting\Ub8SMW;
use AppBundle\SourceFetcher\SourceFetcher;
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

        if ($input->getOption('pm10')) {
            $this->fetchPM10($output, $dateTime);
        }

        if ($input->getOption('so2')) {
            $this->fetchSO2($output, $dateTime);
        }

        if ($input->getOption('no2')) {
            $this->fetchNO2($output, $dateTime);
        }

        if ($input->getOption('o3')) {
            $this->fetchO3($output, $dateTime);
        }

        if ($input->getOption('co')) {
            $this->fetchCO($output, $dateTime);
        }
    }

    protected function fetchPM10(OutputInterface $output, \DateTimeInterface $dateTime)
    {
        $output->writeln('PM10');

        $reporting = new Ub1TMW($dateTime);
        $query = new UbPM10Query($reporting);

        $this->fetch($output, $query, PollutantInterface::POLLUTANT_PM10);
    }

    protected function fetchSO2(OutputInterface $output, \DateTimeInterface $dateTime)
    {
        $output->writeln('SO2');

        $reporting = new Ub1SMW($dateTime);
        $query = new UbSO2Query($reporting);

        $this->fetch($output, $query, PollutantInterface::POLLUTANT_SO2);
    }

    protected function fetchNO2(OutputInterface $output, \DateTimeInterface $dateTime)
    {
        $output->writeln('NO2');

        $reporting = new Ub1SMW($dateTime);
        $query = new UbNO2Query($reporting);

        $this->fetch($output, $query, PollutantInterface::POLLUTANT_NO2);
    }

    protected function fetchO3(OutputInterface $output, \DateTimeInterface $dateTime)
    {
        $output->writeln('O3');

        $reporting = new Ub1SMW($dateTime);
        $query = new UbO3Query($reporting);

        $this->fetch($output, $query, PollutantInterface::POLLUTANT_O3);
    }

    protected function fetchCO(OutputInterface $output, \DateTimeInterface $dateTime)
    {
        $output->writeln('CO');

        $reporting = new Ub8SMW($dateTime);
        $query = new UbCOQuery($reporting);

        $this->fetch($output, $query, PollutantInterface::POLLUTANT_CO);
    }

    protected function fetch(OutputInterface $output, AbstractQuery $query, int $pollutant)
    {
        $sourceFetcher = new SourceFetcher();

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