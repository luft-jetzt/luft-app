<?php declare(strict_types=1);

namespace App\Provider\EuropeanEnvironmentAgency\SourceFetcher;

use App\Pollution\Pollutant\PollutantInterface;
use App\Pollution\PollutantList\PollutantListInterface;
use App\Provider\EuropeanEnvironmentAgency\SourceFetcher\Loader\Loader;
use App\Provider\EuropeanEnvironmentAgency\SourceFetcher\Parser\CsvParserInterface;

class SourceFetcher
{
    /** @var Loader $loader */
    protected $loader;

    /** @var PollutantListInterface $pollutantList */
    protected $pollutantList;

    /** @var array $valueList */
    protected $valueList = [];

    /** @var CsvParserInterface $parser */
    protected $parser;

    /** @var array $countryList */
    protected $countryList = ['ad', 'at', 'be', 'ch', 'cz', 'dk', 'es', 'fi', 'fr', 'gi', 'hr', 'hu', 'ie', 'lt', 'lu', 'lv', 'mk', 'mt', 'pt', 'rs', 'sk'];

    public function __construct(Loader $loader, CsvParserInterface $parser, PollutantListInterface $pollutantList)
    {
        $this->loader = $loader;
        $this->pollutantList = $pollutantList;
        $this->parser = $parser;
    }

    public function setCountryList(array $countryList = []): SourceFetcher
    {
        $this->countryList = $countryList;

        return $this;
    }

    public function process(callable $countryCallback = null, callable $pollutantCallback = null): void
    {
        foreach ($this->countryList as $countryCode) {
            if ($countryCallback) {
                $countryCallback($countryCode);
            }

            /** @var PollutantInterface $pollutant */
            foreach ($this->pollutantList->getPollutants() as $pollutant) {
                if ($pollutantCallback) {
                    $pollutantCallback($countryCode, $pollutant);
                }

                $csvContent = $this->loader->query($pollutant, $countryCode);

                $valueList = $this->parser->parse($csvContent);

                $this->valueList += $valueList;
            }
        }
    }

    public function getValueList(): array
    {
        return $this->valueList;
    }
}
