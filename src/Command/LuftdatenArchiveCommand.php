<?php declare(strict_types=1);

namespace App\Command;

use App\Provider\Luftdaten\LuftdatenProvider;
use App\Provider\Luftdaten\SourceFetcher\ArchiveFetcher\ArchiveFetcherInterface;
use App\Provider\Luftdaten\SourceFetcher\ArchiveSourceFetcherInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LuftdatenArchiveCommand extends ContainerAwareCommand
{
    protected ArchiveFetcherInterface $archiveFetcher;
    protected ArchiveSourceFetcherInterface $archiveSourceFetcher;
    protected LuftdatenProvider $provider;

    public function __construct(?string $name = null, ArchiveSourceFetcherInterface $archiveSourceFetcher,  ArchiveFetcherInterface $archiveFetcher, LuftdatenProvider $luftdatenProvider)
    {
        $this->archiveFetcher = $archiveFetcher;
        $this->archiveSourceFetcher = $archiveSourceFetcher;
        $this->provider = $luftdatenProvider;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setName('luft:luftdaten-archive')
            ->setDescription('')
            ->addOption('page-size', null, InputOption::VALUE_OPTIONAL, '', 100)
            ->addArgument('date', InputArgument::REQUIRED, 'Date of data to fetch');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $dateTime = new \DateTime($input->getArgument('date'));

         $this->archiveSourceFetcher
            ->setDateTime($dateTime)
            ->fetchStationCsvFiles();

        $csvLinkList = $this->archiveSourceFetcher->getCsvLinkList();

        $progressBar = new ProgressBar($output, count($csvLinkList));

        $offset = 0;
        $pageSize = (int) $input->getOption('page-size');
        $maxOffset = floor(count($csvLinkList) / $pageSize);
        $counter = 0;

        for ($offset = 0; $offset <= $maxOffset; ++$offset) {
            $offsetLinkList = array_slice($csvLinkList, $offset * $pageSize, $pageSize);

            $this->archiveFetcher->setCsvLinkList($offsetLinkList);

            $valueList = $this->archiveFetcher->fetch(function () use ($progressBar) {
                $progressBar->advance();
            });

            foreach ($valueList as $value) {
                $this->getContainer()->get('old_sound_rabbit_mq.luft_value_producer')->publish(serialize($value));
            }

            $counter += count($valueList);
        }

        $progressBar->finish();

        $output->writeln(sprintf('Wrote <info>%d</info> values to cache', $counter));
    }
}
