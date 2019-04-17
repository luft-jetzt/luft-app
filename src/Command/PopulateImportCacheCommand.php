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

class PopulateImportCacheCommand extends Command
{
    /** @var CacheUniqueStrategy $cacheUniqueStrategy */
    protected $cacheUniqueStrategy;

    /** @var RegistryInterface $registry */
    protected $registry;

    protected static $defaultName = 'luft:populate-import-cache';

    protected function configure(): void
    {
        $this->setDescription('Add a short description for your command');
    }

    public function __construct(?string $name = null, CacheUniqueStrategy $cacheUniqueStrategy, RegistryInterface $registry)
    {
        $this->cacheUniqueStrategy = $cacheUniqueStrategy->init([]);
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $dataList = $this->registry->getRepository(Data::class)->findAll();

        $this->cacheUniqueStrategy->addDataList($dataList)->save();
    }
}
