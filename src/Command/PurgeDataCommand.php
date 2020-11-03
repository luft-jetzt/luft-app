<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\Data;
use App\Provider\ProviderListInterface;
use App\Util\DateTimeUtil;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PurgeDataCommand extends Command
{
    protected ProviderListInterface $providerList;

    protected ManagerRegistry $registry;

    public function __construct(?string $name = null, ProviderListInterface $providerList, ManagerRegistry $registry)
    {
        $this->providerList = $providerList;
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('luft:purge-data')
            ->addArgument('provider', InputArgument::REQUIRED)
            ->addArgument('days', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $providerIdentifier = $input->getArgument('provider');

        $provider = $this->providerList->getProvider($providerIdentifier);

        if (!$provider) {
            $io->error(sprintf('Could not find provider "%s".', $providerIdentifier));

            return 1;
        }

        $interval = new \DateInterval(sprintf('P%dD', $input->getArgument('days')));
        $untilDateTime = DateTimeUtil::getDayEndDateTime((new \DateTimeImmutable())->sub($interval));

        $dataList = $this->registry->getRepository(Data::class)->findInInterval(null, $untilDateTime, $provider);

        if ('y' !== strtolower($io->ask(sprintf('Purge <info>%d</info> values from <comment>%s</comment> before <info>%s</info>?', count($dataList), get_class($provider), $untilDateTime->format('Y-m-d H:i:s')), 'n'))) {
            return 1;
        }

        $em = $this->registry->getManager();

        $io->progressStart(count($dataList));

        foreach ($dataList as $data) {
            $em->remove($data);

            $io->progressAdvance();
        }

        $io->progressFinish();

        $em->flush();

        $io->success(sprintf('Purged %d values from %s.', count($dataList), get_class($provider)));

        return 0;
    }
}
