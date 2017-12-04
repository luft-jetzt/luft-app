<?php

namespace AppBundle\Command;

use AppBundle\StationLoader\StationLoader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('luft:station')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var StationLoader $stationLoader */
        $stationLoader = $this->getContainer()->get('AppBundle\StationLoader\StationLoader');
        $stationLoader->load();
    }
}
