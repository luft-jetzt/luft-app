<?php declare(strict_types=1);

namespace App\Command;

use App\Pollution\Pollutant\PollutantInterface;
use App\Pollution\Value\Value;
use App\Pollution\ValueCache\ValueCacheInterface;
use App\Provider\EuropeanEnvironmentAgency\EuropeanEnvironmentAgencyProvider;
use App\Provider\Luftdaten\LuftdatenProvider;
use App\Provider\Luftdaten\SourceFetcher\Parser\JsonParserInterface;
use App\Provider\Luftdaten\SourceFetcher\SourceFetcher;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Fetch3Command extends ContainerAwareCommand
{
    /** @var $europeanEnvironmentAgency $provider */
    protected $provider;

    /** @var JsonParserInterface $parser */
    protected $parser;

    /** @var ValueCacheInterface */
    protected $valueCache;

    public function __construct(?string $name = null, ValueCacheInterface $valueCache, EuropeanEnvironmentAgencyProvider $europeanEnvironmentAgency)
    {
        $this->valueCache = $valueCache;
        $this->provider = $europeanEnvironmentAgency;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('luft:eea')
            ->setDescription('')
            ->addArgument('countries', InputArgument::IS_ARRAY, 'List of countries to fetch');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->valueCache->setProvider($this->provider);

        $sourceFetcher = $this->provider->getSourceFetcher();

        if ($input->getArgument('countries')) {
            $sourceFetcher->setCountryList(($input->getArgument('countries')));
        }

        $sourceFetcher->process(function(string $countryCode) use ($output) {
            $output->writeln(sprintf('Current country is: <info>%s</info>', $countryCode));
        }, function(string $countryCode, PollutantInterface $pollutant) use ($output) {
            $output->writeln(sprintf('Current pollutant is: <info>%s</info>', $pollutant->getName()));
        });

        $valueList = $sourceFetcher->getValueList();

        $this->valueCache->setProvider($this->provider)->addValuesToCache($valueList);

        $output->writeln(sprintf('Wrote <info>%d</info> values to cache.', count($valueList)));
    }
}
