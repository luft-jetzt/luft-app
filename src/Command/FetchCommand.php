<?php declare(strict_types=1);

namespace App\Command;

use App\Air\Measurement\MeasurementInterface;
use App\Producer\Value\ValueProducerInterface;
use App\Provider\ProviderInterface;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Parser\Parser;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaCOQuery;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaNO2Query;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaO3Query;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaPM10Query;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaQueryInterface;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaSO2Query;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Reporting\Uba1SMW;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Reporting\Uba8SMW;
use App\Provider\UmweltbundesamtDe\SourceFetcher\SourceFetcher;
use App\Provider\UmweltbundesamtDe\UmweltbundesamtDeProvider;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FetchCommand extends ContainerAwareCommand
{
    /** @var SourceFetcher $fetcher */
    protected $fetcher;

    /** @var ProviderInterface $provider */
    protected $provider;

    /** @var ValueProducerInterface $valueProducer */
    protected $valueProducer;

    public function __construct(?string $name = null, ValueProducerInterface $valueProducer, SourceFetcher $fetcher, UmweltbundesamtDeProvider $umweltbundesamtDeProvider)
    {
        $this->provider = $umweltbundesamtDeProvider;
        $this->fetcher = $fetcher;
        $this->valueProducer = $valueProducer;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('luft:fetch')
            ->setDescription('Load pollution data from Umweltbundesamt')
            ->addOption('pm10')
            ->addOption('so2')
            ->addOption('no2')
            ->addOption('o3')
            ->addOption('co')
            ->addOption('interval', null, InputOption::VALUE_REQUIRED, 'Provide an interval in hours of data to fetch. Is overwritten by startDateTime argument')
            ->addArgument('endDateTime', InputArgument::OPTIONAL)
            ->addArgument('startDateTime', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('endDateTime')) {
            $endDateTime = new \DateTimeImmutable($input->getArgument('endDateTime'));
        } else {
            $endDateTime = new \DateTimeImmutable();
        }

        if ($input->getArgument('startDateTime')) {
            $startDateTime = new \DateTimeImmutable($input->getArgument('startDateTime'));
        } elseif ($input->getOption('interval')) {
            $startDateTime = $endDateTime->sub(new \DateInterval(sprintf('PT%dH', $input->getOption('interval'))));
        } else {
            $startDateTime = $endDateTime->sub(new \DateInterval(sprintf('PT2H')));
        }

        $output->writeln(sprintf('Fetching uba pollution data from <info>%s</info> to <info>%s</info>', $startDateTime->format('Y-m-d H:i:s'), $endDateTime->format('Y-m-d H:i:s')));

        if ($input->getOption('pm10')) {
            $this->fetchPM10($output, $endDateTime, $startDateTime);
        }

        if ($input->getOption('so2')) {
            $this->fetchSO2($output, $endDateTime, $startDateTime);
        }

        if ($input->getOption('no2')) {
            $this->fetchNO2($output, $endDateTime, $startDateTime);
        }

        if ($input->getOption('o3')) {
            $this->fetchO3($output, $endDateTime, $startDateTime);
        }

        if ($input->getOption('co')) {
            $this->fetchCO($output, $endDateTime, $startDateTime);
        }
    }

    protected function fetchPM10(OutputInterface $output, \DateTimeInterface $endDateTime, \DateTimeInterface $startDateTime = null)
    {
        $output->writeln('PM10');

        $query = new UbaPM10Query();

        $this->fetch($output, $query, MeasurementInterface::MEASUREMENT_PM10);
    }

    protected function fetchSO2(OutputInterface $output, \DateTimeInterface $endDateTime, \DateTimeInterface $startDateTime = null)
    {
        $output->writeln('SO2');

        $query = new UbaSO2Query();

        $this->fetch($output, $query, MeasurementInterface::MEASUREMENT_SO2);
    }

    protected function fetchNO2(OutputInterface $output, \DateTimeInterface $endDateTime, \DateTimeInterface $startDateTime = null)
    {
        $output->writeln('NO2');

        $query = new UbaNO2Query();

        $this->fetch($output, $query, MeasurementInterface::MEASUREMENT_NO2);
    }

    protected function fetchO3(OutputInterface $output, \DateTimeInterface $endDateTime, \DateTimeInterface $startDateTime = null)
    {
        $output->writeln('O3');

        $query = new UbaO3Query();

        $this->fetch($output, $query, MeasurementInterface::MEASUREMENT_O3);
    }

    protected function fetchCO(OutputInterface $output, \DateTimeInterface $endDateTime, \DateTimeInterface $startDateTime = null)
    {
        $output->writeln('CO');

        $query = new UbaCOQuery();

        $this->fetch($output, $query, MeasurementInterface::MEASUREMENT_CO);
    }

    protected function fetch(OutputInterface $output, UbaQueryInterface $query, int $pollutant)
    {
        $sourceFetcher = new SourceFetcher();

        $response = $sourceFetcher->query($query);

        $parser = new Parser($query);
        $valueList = $parser->parse($response, $pollutant);

        foreach ($valueList as $value) {
            $this->valueProducer->publish($value);
        }

        $output->writeln(sprintf('Wrote <info>%d</info> values to cache.', count($valueList)));
    }
}
