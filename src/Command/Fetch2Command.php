<?php declare(strict_types=1);

namespace App\Command;

use App\Producer\Value\ValueProducerInterface;
use App\Provider\Luftdaten\LuftdatenProvider;
use App\Provider\Luftdaten\SourceFetcher\Parser\JsonParserInterface;
use App\Provider\Luftdaten\SourceFetcher\SourceFetcher;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Fetch2Command extends ContainerAwareCommand
{
    /** @var LuftdatenProvider $provider */
    protected $provider;

    /** @var JsonParserInterface $parser */
    protected $parser;

    /** @var ValueProducerInterface $valueProducer */
    protected $valueProducer;

    public function __construct(?string $name = null, ValueProducerInterface $valueProducer, LuftdatenProvider $luftdatenProvider, JsonParserInterface $parser)
    {
        $this->provider = $luftdatenProvider;
        $this->parser = $parser;
        $this->valueProducer = $valueProducer;

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

        $valueList = $this->parser->parse($response);

        foreach ($valueList as $value) {
            $this->valueProducer->publish($value);
        }

        $output->writeln(sprintf('Wrote <info>%d</info> values to cache.', count($valueList)));
    }
}
