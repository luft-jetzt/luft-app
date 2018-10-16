<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\Station;
use App\StationLoader\StationLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StationCommand extends Command
{
    /** @var StationLoader $stationLoader */
    protected $stationLoader;

    public function __construct(?string $name = null, StationLoader $stationLoader)
    {
        $this->stationLoader = $stationLoader;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('luft:station')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->stationLoader->load();

        $progressBar = new ProgressBar($output, $this->stationLoader->count());

        $this->stationLoader->process(function() use ($progressBar) {
            $progressBar->advance();
        });

        $progressBar->finish();

        $output->writeln('Existing stations');
        $this->printTable($output, $this->stationLoader->getExistingStationList());

        $output->writeln('');

        $output->writeln('New stations');
        $this->printTable($output, $this->stationLoader->getNewStationList());
    }

    protected function printTable(OutputInterface $output, array $stationList): void
    {
        $table = new Table($output);
        $table->setHeaders(['stationCode', 'stateCode', 'title', 'latitude', 'longitude', 'fromDate', 'untilDate']);

        foreach ($stationList as $station) {
            $this->addStationRow($table, $station);
        }

        $table->render();
    }

    protected function addStationRow(Table $table, Station $station): void
    {
        $table->addRow([
            $station->getStationCode(),
            $station->getStateCode(),
            $station->getTitle(),
            $station->getLatitude(),
            $station->getLongitude(),
            $station->getFromDate() ? $station->getFromDate()->format('Y-m-d') : '',
            $station->getUntilDate() ? $station->getUntilDate()->format('Y-m-d') : '',
        ]);
    }
}
