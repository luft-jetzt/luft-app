<?php declare(strict_types=1);

namespace App\Command;

use App\Pollution\DataPersister\UniquePersisterInterface;
use App\Provider\ProviderInterface;
use App\Provider\ProviderListInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StoreValuesCommand extends Command
{
    /** @var UniquePersisterInterface $uniquePersister */
    protected $uniquePersister;

    /** @var ProviderListInterface $providerList */
    protected $providerList;

    public function __construct(?string $name = null, UniquePersisterInterface $uniquePersister, ProviderListInterface $providerList)
    {
        $this->uniquePersister = $uniquePersister;
        $this->providerList = $providerList;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('luft:store-values');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $client = RedisAdapter::createConnection('redis://localhost');

        $cache = new RedisAdapter($client);

        /** @var ProviderInterface $provider */
        foreach ($this->providerList->getList() as $identifier => $provider) {
            $output->writeln(sprintf('Looking up cache for <info>%s</info>', get_class($provider)));

            $key = sprintf('values-%s', $identifier);

            $item = $cache->getItem($key);

            $valueList = $item->get() ?? [];

            $this->uniquePersister
                ->setProvider($provider)
                ->persistValues($valueList);

            $output->writeln(sprintf('Persisted <info>%d</info> new values, skipped <info>%d</info> existent values.', count($this->uniquePersister->getNewValueList()), count($this->uniquePersister->getDuplicateDataList())));
        }


    }
}
