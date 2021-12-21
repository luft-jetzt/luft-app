<?php declare(strict_types=1);

namespace App\Command\ImportCache;

use App\Entity\Data;
use App\Pollution\UniqueStrategy\CacheUniqueStrategy;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PopulateImportCacheCommand extends Command
{
    /** @var CacheUniqueStrategy $cacheUniqueStrategy */
    protected $cacheUniqueStrategy;

    protected ManagerRegistry $registry;

    protected static $defaultName = 'luft:import-cache:populate';

    protected function configure(): void
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addOption('interval', 'i', InputOption::VALUE_REQUIRED, 'Provide an interval starting from today backwards', 'P3D');
    }

    public function __construct(?string $name = null, CacheUniqueStrategy $cacheUniqueStrategy, ManagerRegistry $registry)
    {
        $this->cacheUniqueStrategy = $cacheUniqueStrategy->init([]);
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $intervalSpec = $input->getOption('interval');

        $interval = new \DateInterval($intervalSpec);

        $untilDateTime = new \DateTimeImmutable();
        $fromDateTime = $untilDateTime->sub($interval);

        $dataList = $this->registry->getRepository(Data::class)->findInInterval($fromDateTime, $untilDateTime);

        $this->cacheUniqueStrategy->addDataList($dataList)->save();

        $io = new SymfonyStyle($input, $output);
        $io->success(sprintf('Stored %d data elements from %s to %s', count($dataList), $fromDateTime->format('Y-m-d H:i:s'), $untilDateTime->format('Y-m-d H:i:s')));
    }
}
