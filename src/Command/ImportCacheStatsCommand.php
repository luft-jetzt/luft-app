<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\Data;
use App\Pollution\UniqueStrategy\CacheUniqueStrategy;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCacheStatsCommand extends Command
{
    /** @var CacheUniqueStrategy $cacheUniqueStrategy */
    protected $cacheUniqueStrategy;

    protected static $defaultName = 'luft:import-cache:stats';

    protected function configure(): void
    {
        $this
            ->setDescription('Add a short description for your command');
    }

    public function __construct(?string $name = null, CacheUniqueStrategy $cacheUniqueStrategy)
    {
        $this->cacheUniqueStrategy = $cacheUniqueStrategy->init([]);

        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
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
    }
}
