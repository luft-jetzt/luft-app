<?php declare(strict_types=1);

namespace App\Command;

use App\DataPurger\DataPurgerInterface;
use App\Provider\ProviderListInterface;
use Carbon\CarbonImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'luft:purge-data',
    description: 'Purge data from elasticsearch'
)]
class PurgeDataCommand extends Command
{
    public function __construct(protected ProviderListInterface $providerList, protected DataPurgerInterface $dataPurger)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('days', InputArgument::REQUIRED, 'Specify number of days. Data older than this value will be purged.')
            ->addArgument('provider', InputArgument::OPTIONAL, 'Optional: Specify provider to purge.')
            ->addOption('with-tags', 'wt', InputOption::VALUE_NONE, 'Also delete tagged data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $providerIdentifier = $input->getArgument('provider');

        $provider = $this->providerList->getProvider($providerIdentifier ?? '');

        if ($providerIdentifier && !$provider) {
            $io->error(sprintf('Could not find provider "%s".', $providerIdentifier));

            return 1;
        }

        $interval = new \DateInterval(sprintf('P%dD', $input->getArgument('days')));
        $untilDateTime = CarbonImmutable::now()->sub($interval);

        $provider = $this->providerList->getProvider($input->getArgument('provider'));

        $dataList = $this->registry->getRepository(Data::class)->findInInterval(null, $untilDateTime, $provider);

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(sprintf('Purge <info>%d</info> values from <comment>%s</comment> before <info>%s</info>? [no] ', is_countable($dataList) ? count($dataList) : 0, $provider::class, $untilDateTime->format('Y-m-d H:i:s')), false);

        if (!$helper->ask($input, $output, $question)) {
            return 1;
        }

        $counter = $this->dataPurger->purgeData($untilDateTime, $provider, $input->getOption('with-tags'));

        if ($provider) {
            $io->success(sprintf('Purged %d values from %s.', $counter, $provider::class));
        } else {
            $io->success(sprintf('Purged %d values.', $counter));
        }

        //return Command::SUCCESS;
        return 0;
    }
}
