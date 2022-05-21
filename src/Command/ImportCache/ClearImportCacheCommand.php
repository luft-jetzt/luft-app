<?php declare(strict_types=1);

namespace App\Command\ImportCache;

use App\Pollution\UniqueStrategy\UniqueStrategyInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ClearImportCacheCommand extends Command
{
    protected UniqueStrategyInterface $cacheUniqueStrategy;

    protected static $defaultName = 'luft:import-cache:clear';

    protected function configure(): void
    {
        $this->setDescription('Add a short description for your command');
    }

    public function __construct(UniqueStrategyInterface $cacheUniqueStrategy)
    {
        $this->cacheUniqueStrategy = $cacheUniqueStrategy->init([]);

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->cacheUniqueStrategy->clear();

        $io->success('Import cache cleared.');

        //return Command::SUCCESS;
        return 0;
    }
}
