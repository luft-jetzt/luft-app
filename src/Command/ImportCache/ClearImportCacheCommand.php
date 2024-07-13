<?php declare(strict_types=1);

namespace App\Command\ImportCache;

use App\Pollution\UniqueStrategy\UniqueStrategyInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'luft:import-cache:clear',
    description: 'Clear import cache'
)]
class ClearImportCacheCommand extends Command
{
    protected UniqueStrategyInterface $cacheUniqueStrategy;

    public function __construct(UniqueStrategyInterface $cacheUniqueStrategy)
    {
        $this->cacheUniqueStrategy = $cacheUniqueStrategy->init([]);

        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->cacheUniqueStrategy->clear();

        $io->success('Import cache cleared.');

        //return Command::SUCCESS;
        return 0;
    }
}
