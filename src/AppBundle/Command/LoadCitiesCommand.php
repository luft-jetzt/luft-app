<?php

namespace AppBundle\Command;

use AppBundle\CityLoader\ZipLoader;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadCitiesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('luft:zip:load')
            ->setDescription('Load zip codes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getContainer()->get('doctrine')->getManager();

        $zipLoader = new ZipLoader();

        $zipLoader->loadData();

        $progress = new ProgressBar($output, $zipLoader->countData());
        $progress->start();

        while ($zipLoader->hasData()) {
            $zip = $zipLoader->parseData();

            if ($zip) {
                $entityManager->persist($zip);
            }

            $progress->advance();
        }

        $entityManager->flush();
        $progress->finish();
    }
}
