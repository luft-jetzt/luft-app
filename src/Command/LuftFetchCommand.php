<?php declare(strict_types=1);

namespace App\Command;

use App\Air\Measurement\MeasurementInterface;
use App\Air\MeasurementList\MeasurementListInterface;
use App\Provider\ProviderInterface;
use App\Provider\ProviderListInterface;
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
            ->addArgument('pollutants', InputArgument::IS_ARRAY, 'List of pollutants to fetch', []);
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

        /** @var ProviderInterface $provider */
        foreach ($providersToQuery as $provider) {
            $provider->fetchMeasurements($measurementsToQuery);
        }

        return 1;
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
}
