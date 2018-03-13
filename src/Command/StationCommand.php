<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\Station;
use App\StationLoader\StationLoader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StationCommand extends ContainerAwareCommand
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
