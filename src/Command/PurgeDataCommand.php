<?php declare(strict_types=1);

namespace App\Command;

use App\DataPurger\DataPurgerInterface;
use App\Provider\ProviderListInterface;
use App\Util\DateTimeUtil;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PurgeDataCommand extends Command
{
    protected ProviderListInterface $providerList;
    protected DataPurgerInterface $dataPurger;

    public function __construct(ProviderListInterface $providerList, DataPurgerInterface $dataPurger)
    {
        $this->providerList = $providerList;
        $this->dataPurger = $dataPurger;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('luft:purge-data')
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
        $untilDateTime = DateTimeUtil::getDayEndDateTime((new \DateTime())->sub($interval));

        $counter = $this->dataPurger->purgeData($untilDateTime, $provider, $input->getOption('with-tags'));

        if ($provider) {
            $io->success(sprintf('Purged %d values from %s.', $counter, get_class($provider)));
        } else {
            $io->success(sprintf('Purged %d values.', $counter));
        }

        return 0;
    }
}
