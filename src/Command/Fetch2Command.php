<?php declare(strict_types=1);

namespace App\Command;

use App\Pollution\Value\Value;
use App\Pollution\ValueCache\ValueCacheInterface;
use App\Provider\Luftdaten\LuftdatenProvider;
use App\Provider\Luftdaten\SourceFetcher\Parser\JsonParserInterface;
use App\Provider\Luftdaten\SourceFetcher\SourceFetcher;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Fetch2Command extends ContainerAwareCommand
{
    /** @var LuftdatenProvider $provider */
    protected $provider;

    /** @var JsonParserInterface $parser */
    protected $parser;

    /** @var ValueCacheInterface */
    protected $valueCache;

    public function __construct(?string $name = null, ValueCacheInterface $valueCache, LuftdatenProvider $luftdatenProvider, JsonParserInterface $parser)
    {
        $this->valueCache = $valueCache;
        $this->provider = $luftdatenProvider;
        $this->parser = $parser;

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

        $this->valueCache->addValuesToCache($this->provider, $valueList);

        $output->writeln(sprintf('Wrote <info>%d</info> values to cache.', count($valueList)));
    }
}
