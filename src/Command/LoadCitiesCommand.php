<?php declare(strict_types=1);

namespace App\Command;

use App\CityLoader\ZipLoader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadCitiesCommand extends Command
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('luft:load-cities')
            ->setDescription('Load zip codes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $zipLoader = new ZipLoader();

        $zipLoader->loadData();

        $progress = new ProgressBar($output, $zipLoader->countData());
        $progress->start();

        while ($zipLoader->hasData()) {
            $zipEntityList = $zipLoader->parseData();

            foreach ($zipEntityList as $zipEntity) {
                $this->entityManager->persist($zipEntity);
            }

            $progress->advance();
        }

        $this->entityManager->flush();
        $progress->finish();

        //return Command::SUCCESS;
        return 0;
    }
}
