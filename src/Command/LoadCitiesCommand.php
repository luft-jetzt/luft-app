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
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(?string $name = null, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('luft:zip:load')
            ->setDescription('Load zip codes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
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
    }
}
