<?php

namespace App\Command;

use App\Entity\Data;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'luft:refresh',
    description: 'Add a short description for your command',
)]
class LuftRefreshCommand extends Command
{
    public function __construct(string $name = null, protected ManagerRegistry $managerRegistry)
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->managerRegistry->getRepository(Data::class)->refreshMaterializedView();

        return Command::SUCCESS;
    }
}
