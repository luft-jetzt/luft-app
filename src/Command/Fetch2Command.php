<?php declare(strict_types=1);

namespace App\Command;

use App\Pollution\DataPersister\UniquePersisterInterface;
use App\Pollution\Value\Value;
use App\Provider\Luftdaten\SourceFetcher\Parser\Parser;
use App\Provider\Luftdaten\SourceFetcher\SourceFetcher;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Fetch2Command extends ContainerAwareCommand
{
    /** @var UniquePersisterInterface */
    protected $persister;

    public function __construct(?string $name = null, UniquePersisterInterface $persister)
    {
        $this->persister = $persister;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('luft:luftdaten')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $sourceFetcher = new SourceFetcher();

        $response = $sourceFetcher->query();

        $parser = new Parser();
        $tmpValueList = $parser->parse($response);

        $this->persister->persistValues($tmpValueList);

        $this->writeValueTable($output, $this->persister->getNewValueList());

        $output->writeln(sprintf('Persisted <info>%d</info> new values, skipped <info>%d</info> existent values.', count($this->persister->getNewValueList()), count($this->persister->getDuplicateDataList())));
    }

    protected function writeValueTable(OutputInterface $output, array $newValueList): void
    {
        $table = new Table($output);
        $table->setHeaders(['Station', 'Title', 'Value', 'DateTime']);

        /** @var Value $value */
        foreach ($newValueList as $value) {
            $table->addRow([$value->getStation()->getStationCode(), $value->getStation()->getTitle(), $value->getValue(), $value->getDateTime()->format('Y-m-d H:i:s')]);
        }

        $table->render();
    }
}
