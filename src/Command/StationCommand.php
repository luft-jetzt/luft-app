<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\Station;
use App\Provider\ProviderInterface;
use App\Provider\ProviderListInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StationCommand extends Command
{
    protected ProviderListInterface $providerList;

    protected static $defaultName = 'luft:load-station';

    public function __construct(ProviderListInterface $providerList)
    {
        $this->providerList = $providerList;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('update', 'u', InputOption::VALUE_NONE, 'Update existing station data')
            ->addArgument('provider', InputArgument::REQUIRED, 'Providers to fetch from')
            ->setDescription('Fetch station list')
        ;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var ProviderInterface $provider */
        foreach ($this->providerList->getList() as $provider) {
            if ($input->getArgument('provider') !== $provider->getIdentifier()) {
                continue;
            }

            $stationLoader = $provider->getStationLoader();

            $stationLoader->load();

            if ($input->getOption('update')) {
                $stationLoader->setUpdate(true);
            }

            $progressBar = new ProgressBar($output, $stationLoader->count());

            $stationLoader->process(function() use ($progressBar) {
                $progressBar->advance();
            });

            $progressBar->finish();

            $output->writeln('Existing stations');
            $this->printTable($output, $stationLoader->getExistingStationList());

            $output->writeln('');

            $output->writeln('New stations');
            $this->printTable($output, $stationLoader->getNewStationList());
        }

        //return Command::SUCCESS;
        return 0;
    }

    protected function printTable(OutputInterface $output, array $stationList): void
    {
        $table = new Table($output);
        $table->setHeaders(['stationCode', 'title', 'latitude', 'longitude', 'altitude', 'fromDate', 'untilDate', 'stationType']);

        foreach ($stationList as $station) {
            $this->addStationRow($table, $station);
        }

        $table->render();
    }

    protected function addStationRow(Table $table, Station $station): void
    {
        $table->addRow([
            $station->getStationCode(),
            $station->getTitle(),
            $station->getLatitude(),
            $station->getLongitude(),
            $station->getAltitude() ?? '',
            $station->getFromDate() ? $station->getFromDate()->format('Y-m-d') : '',
            $station->getUntilDate() ? $station->getUntilDate()->format('Y-m-d') : '',
            $station->getStationType(),
        ]);
    }
}
