<?php declare(strict_types=1);

namespace App\Command\ImportCache;

use App\Entity\Data;
use App\Pollution\UniqueStrategy\UniqueStrategyInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'luft:import-cache:populate',
    description: 'Populate import cache'
)]
class PopulateImportCacheCommand extends Command
{
    protected UniqueStrategyInterface $cacheUniqueStrategy;

    #[\Override]
    protected function configure(): void
    {
        $this->addOption('interval', 'i', InputOption::VALUE_REQUIRED, 'Provide an interval starting from today backwards', 'P3D');
    }

    public function __construct(UniqueStrategyInterface $cacheUniqueStrategy, protected ManagerRegistry $registry)
    {
        $this->cacheUniqueStrategy = $cacheUniqueStrategy->init([]);

        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $intervalSpec = $input->getOption('interval');

        $interval = new \DateInterval($intervalSpec);

        $untilDateTime = new \DateTimeImmutable();
        $fromDateTime = $untilDateTime->sub($interval);

        $dataList = $this->registry->getRepository(Data::class)->findInInterval($fromDateTime, $untilDateTime);

        $this->cacheUniqueStrategy->addDataList($dataList)->save();

        $io = new SymfonyStyle($input, $output);
        $io->success(sprintf('Stored %d data elements from %s to %s', is_countable($dataList) ? count($dataList) : 0, $fromDateTime->format('Y-m-d H:i:s'), $untilDateTime->format('Y-m-d H:i:s')));

        //return Command::SUCCESS;
        return 0;
    }
}
