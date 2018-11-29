<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\Data;
use App\Provider\ProviderListInterface;
use App\Util\DateTimeUtil;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class PurgeDataCommand extends Command
{
    /** @var ProviderListInterface $providerList */
    protected $providerList;

    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(?string $name = null, ProviderListInterface $providerList, RegistryInterface $registry)
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

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $interval = new \DateInterval(sprintf('P%dD', $input->getArgument('days')));
        $untilDateTime = DateTimeUtil::getDayEndDateTime((new \DateTimeImmutable())->sub($interval));

        $provider = $this->providerList->getProvider($input->getArgument('provider'));

        $dataList = $this->registry->getRepository(Data::class)->findInInterval(null, $untilDateTime, $provider);

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(sprintf('Purge <info>%d</info> values from <comment>%s</comment> before <info>%s</info>? [no] ', count($dataList), get_class($provider), $untilDateTime->format('Y-m-d H:i:s')), false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $em = $this->registry->getManager();

        foreach ($dataList as $data) {
            $em->remove($data);
        }

        $em->flush();

        $output->writeln(sprintf('Purged <info>%d</info> values from <comment>%s</comment>', count($dataList), get_class($provider)));
    }
}
