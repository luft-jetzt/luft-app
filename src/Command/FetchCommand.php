<?php declare(strict_types=1);

namespace App\Command;

use App\Pollution\Pollutant\PollutantInterface;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Parser\Parser;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Persister\PersisterInterface;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaCOQuery;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaNO2Query;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaO3Query;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaPM10Query;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaQueryInterface;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaSO2Query;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Reporting\Uba1SMW;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Reporting\Uba1TMW;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Reporting\Uba8SMW;
use App\Provider\UmweltbundesamtDe\SourceFetcher\SourceFetcher;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Value\Value;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchCommand extends Command
{
    /** @var PersisterInterface $persister */
    protected $persister;

    /** @var SourceFetcher $fetcher */
    protected $fetcher;

    public function __construct(?string $name = null, PersisterInterface $persister, SourceFetcher $fetcher)
    {
        $this->persister = $persister;
        $this->fetcher = $fetcher;

        parent::__construct($name);
    }

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

        $reporting = new Uba1TMW($dateTime);
        $query = new UbaPM10Query($reporting);

        $this->fetch($output, $query, PollutantInterface::POLLUTANT_PM10);
    }

    protected function fetchSO2(OutputInterface $output, \DateTimeInterface $dateTime)
    {
        $output->writeln('SO2');

        $reporting = new Uba1SMW($dateTime);
        $query = new UbaSO2Query($reporting);

        $this->fetch($output, $query, PollutantInterface::POLLUTANT_SO2);
    }

    protected function fetchNO2(OutputInterface $output, \DateTimeInterface $dateTime)
    {
        $output->writeln('NO2');

        $reporting = new Uba1SMW($dateTime);
        $query = new UbaNO2Query($reporting);

        $this->fetch($output, $query, PollutantInterface::POLLUTANT_NO2);
    }

    protected function fetchO3(OutputInterface $output, \DateTimeInterface $dateTime)
    {
        $output->writeln('O3');

        $reporting = new Uba1SMW($dateTime);
        $query = new UbaO3Query($reporting);

        $this->fetch($output, $query, PollutantInterface::POLLUTANT_O3);
    }

    protected function fetchCO(OutputInterface $output, \DateTimeInterface $dateTime)
    {
        $output->writeln('CO');

        $reporting = new Uba8SMW($dateTime);
        $query = new UbaCOQuery($reporting);

        $this->fetch($output, $query, PollutantInterface::POLLUTANT_CO);
    }

    protected function fetch(OutputInterface $output, UbaQueryInterface $query, int $pollutant)
    {
        $sourceFetcher = new SourceFetcher();

        $response = $sourceFetcher->query($query);

        $parser = new Parser($query);
        $tmpValueList = $parser->parse($response, $pollutant);

        $this->persister->persistValues($tmpValueList);

        $this->writeValueTable($output, $this->persister->getNewValueList());
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
