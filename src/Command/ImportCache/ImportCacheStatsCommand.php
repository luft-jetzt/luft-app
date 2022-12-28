<?php declare(strict_types=1);

namespace App\Command\ImportCache;

use App\Pollution\UniqueStrategy\UniqueStrategyInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'luft:import-cache:stats',
    description: 'Print stats for import cache'
)]
class ImportCacheStatsCommand extends Command
{
    protected UniqueStrategyInterface $cacheUniqueStrategy;

    public function __construct(UniqueStrategyInterface $cacheUniqueStrategy)
    {
        $this->cacheUniqueStrategy = $cacheUniqueStrategy->init([]);

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dataList = $this->cacheUniqueStrategy->init([])->getDataList();

        $dayList = [];

        $io = new SymfonyStyle($input, $output);

        foreach ($dataList as $hash => $timestamp) {
            $date = (new \DateTime(sprintf('@%d', $timestamp)))->format('Y-m-d');

            if (array_key_exists($date, $dayList)) {
                ++$dayList[$date];
            } else {
                $dayList[$date] = 1;
            }
        }

        $rows = [];

        foreach ($dayList as $day => $counter) {
            $rows[$day] = [$day, $counter];
        }

        ksort($rows);

        $io->table(['DateTime', 'Counter'], $rows);

        //return Command::SUCCESS;
        return 0;
    }
}
