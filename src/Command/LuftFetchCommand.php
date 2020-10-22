<?php declare(strict_types=1);

namespace App\Command;

use App\Air\Measurement\MeasurementInterface;
use App\Air\MeasurementList\MeasurementListInterface;
use App\Provider\ProviderInterface;
use App\Provider\ProviderListInterface;
use App\SourceFetcher\FetchProcess;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LuftFetchCommand extends Command
{
    protected static $defaultName = 'luft:fetch';

    protected ProviderListInterface $providerList;

    protected MeasurementListInterface $measurementList;

    public function __construct(string $name = null, ProviderListInterface $providerList, MeasurementListInterface $measurementList)
    {
        $this->providerList = $providerList;
        $this->measurementList = $measurementList;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Fetch new values')
            ->addArgument('pollutants', InputArgument::IS_ARRAY, 'List of pollutants to fetch', [])
            ->addOption('fromDateTime', null, InputOption::VALUE_REQUIRED)
            ->addOption('untilDateTime', null,InputOption::VALUE_REQUIRED)
            ->addOption('interval', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $measurementsToQuery = $this->getMeasurementsToQuery($input);

        if (!$measurementsToQuery) {
            $io->error('Please provide at least one pollutant to fetch');

            return 1;
        }

        $providersToQuery = $this->getProvidersForMeasurements($measurementsToQuery);

        if (!$providersToQuery) {
            $io->warning(sprintf('There are no providers to query: %s', join(', ', $input->getArgument('pollutants'))));
        }

        $fetchProcess = $this->createFetchProcess($measurementsToQuery, $input);

        /** @var ProviderInterface $provider */
        foreach ($providersToQuery as $provider) {
            $io->text(sprintf('Will now query provider %s for %s', $provider->getIdentifier(), join(', ', array_keys($measurementsToQuery))));

            $fetchResult = $provider->fetchMeasurements($fetchProcess);

            /**
             * @var string $identifier
             * @var int $counter
             */
            foreach ($fetchResult->getCounters() as $identifier => $counter) {
                $io->success(sprintf('Provider %s returned %d new values for %s', $provider->getIdentifier(), $counter, $identifier));
            }
        }

        return 0;
    }

    protected function getMeasurementsToQuery(InputInterface $input): array
    {
        $inputMeasurements = $input->getArgument('pollutants');
        $measurementsToQuery = [];

        /** @var MeasurementInterface $measurement */
        foreach ($this->measurementList->getMeasurements() as $measurement) {
            if (in_array($measurement->getIdentifier(), $inputMeasurements)) {
                $measurementsToQuery[$measurement->getIdentifier()] = $measurement;
            }
        }

        return $measurementsToQuery;
    }

    protected function getProvidersForMeasurements(array $measurementsToQuery): array
    {
        $providersToQuery = [];

        /** @var MeasurementInterface $measurement */
        foreach ($measurementsToQuery as $measurement) {
            /** @var ProviderInterface $provider */
            foreach ($this->providerList->getList() as $provider) {
                if ($provider->providesMeasurement($measurement)) {
                    $providersToQuery[$provider->getIdentifier()] = $provider;
                }
            }
        }

        return $providersToQuery;
    }

    protected function createFetchProcess(array $measurementsToQuery, InputInterface $input): FetchProcess
    {
        $fetchProcess = new FetchProcess();

        $fetchProcess->setMeasurementList($measurementsToQuery);

        if ($input->getOption('fromDateTime')) {
            $fetchProcess->setFromDateTime(new \DateTimeImmutable($input->getOption('fromDateTime')));
        }

        if ($input->getOption('untilDateTime')) {
            $fetchProcess->setUntilDateTime(new \DateTimeImmutable($input->getOption('untilDateTime')));
        }

        if ($input->getOption('interval')) {
            $fetchProcess->setInterval(new \DateInterval(sprintf('PT%dH', (int) $input->getOption('interval'))));
        }

        return $fetchProcess;
    }
}
