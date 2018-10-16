<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\Station;
use App\StationLoader\StationLoader;
use Symfony\Component\Console\Command\Command;
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

        $output->writeln('New stations');

        $this->printTable($output, $this->stationLoader->getNewStationList());

        $output->writeln('');
        $output->writeln('Existing stations');

        $this->printTable($output, $this->stationLoader->getExistingStationList());
    }

    protected function printTable(OutputInterface $output, array $stationList): void
    {
        $table = new Table($output);
        $table->setHeaders(['stationCode', 'stateCode', 'title', 'latitude', 'longitude']);

        foreach ($stationList as $station) {
            $this->addStationRow($table, $station);
        }

        $table->render();
    }

    protected function addStationRow(Table $table, Station $station): void
    {
        $table->addRow([$station->getStationCode(), $station->getStateCode(), $station->getTitle(), $station->getLatitude(), $station->getLongitude()]);
    }
}
