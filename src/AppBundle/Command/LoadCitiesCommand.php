<?php

namespace AppBundle\Command;

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
            ->setName('cycleways:city:load')
            ->setDescription('Load cities');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getContainer()->get('doctrine')->getManager();

        $cityLoader = new CityLoader();

        $cityLoader->loadData();

        $progress = new ProgressBar($output, $cityLoader->countData());
        $progress->start();

        while ($cityLoader->hasData()) {
            $city = $cityLoader->parseData();

            if ($city) {
                $slug = new Slug($city->getName());
                $city->setSlug($slug);

                $entityManager->persist($city);
            }

            $progress->advance();
        }

        $entityManager->flush();
        $progress->finish();
    }
}
