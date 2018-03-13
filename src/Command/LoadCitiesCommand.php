<?php declare(strict_types=1);

namespace App\Command;

use App\CityLoader\ZipLoader;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadCitiesCommand extends Command
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
            $zipEntityList = $zipLoader->parseData();

            foreach ($zipEntityList as $zipEntity) {
                $entityManager->persist($zipEntity);
            }

            $progress->advance();
        }

        $entityManager->flush();
        $progress->finish();
    }
}
