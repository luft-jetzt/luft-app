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
            ->setDescription('')
            ->addOption('ub')
            ->addOption('ld')
        ;
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('ub')) {
            $this->fetchStation(UbStationLoader::class, $output);
        }

        if ($input->getOption('ld')) {
            $this->fetchStation(LdStationLoader::class, $output);
        }

        $output->writeln('New stations');

        $table = new Table($output);
        $table->setHeaders(['stationCode', 'stateCode', 'title', 'latitude', 'longitude']);

        foreach ($this->stationLoader->getNewStationList() as $newStation) {
            $this->addStationRow($table, $newStation);
        }

        $table->render();

        $output->writeln('');
        $output->writeln('Existing stations');

        $table = new Table($output);
        $table->setHeaders(['stationCode', 'stateCode', 'title', 'latitude', 'longitude']);

        foreach ($this->stationLoader->getExistingStationList() as $existingStation) {
            $this->addStationRow($table, $existingStation);
        }

        $table->render();
    }

    protected function addStationRow(Table $table, Station $station): void
    {
        $table->addRow([$station->getStationCode(), $station->getStateCode(), $station->getTitle(), $station->getLatitude(), $station->getLongitude()]);
    }
}
