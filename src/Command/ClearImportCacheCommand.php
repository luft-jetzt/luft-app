<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\Data;
use App\Pollution\UniqueStrategy\CacheUniqueStrategy;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ClearImportCacheCommand extends Command
{
    /** @var CacheUniqueStrategy $cacheUniqueStrategy */
    protected $cacheUniqueStrategy;

    protected static $defaultName = 'luft:import-cache:clear';

    protected function configure(): void
    {
        $this->setDescription('Add a short description for your command');
    }

    public function __construct(?string $name = null, CacheUniqueStrategy $cacheUniqueStrategy, RegistryInterface $registry)
    {
        $this->cacheUniqueStrategy = $cacheUniqueStrategy->init([]);

        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $this->cacheUniqueStrategy->clear();

        $io->success('Import cache cleared.');
    }
}
