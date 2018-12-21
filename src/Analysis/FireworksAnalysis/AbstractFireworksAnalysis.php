<?php declare(strict_types=1);

namespace App\Analysis\FireworksAnalysis;

use App\Pollution\Pollutant\PM10;
use App\Pollution\Pollutant\PollutantInterface;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

abstract class AbstractFireworksAnalysis implements FireworksAnalysisInterface
{
    /** @var float $minSlope */
    protected $minSlope = 10.0;

    /** @var float $maxSlope */
    protected $maxSlope = 5000.0;

    /** @var PollutantInterface $pollutant */
    protected $pollutant;

    /** @var PaginatedFinderInterface $finder */
    protected $finder;

    /** @var \DateTimeInterface $fromDateTime */
    protected $fromDateTime;

    /** @var \DateTimeInterface $untilDateTime */
    protected $untilDateTime;

    /** @var FireworksModelFactoryInterface $komfortofenModelFactory */
    protected $komfortofenModelFactory;

    public function __construct(PaginatedFinderInterface $finder, FireworksModelFactoryInterface $komfortofenModelFactory)
    {
        $this->finder = $finder;
        $this->komfortofenModelFactory = $komfortofenModelFactory;

        $this->pollutant = new PM10();
        $this->fromDateTime = new \DateTime('2017-12-31 12:00:00');
        $this->untilDateTime = new \DateTime('2018-01-01 12:00:00');
    }
}
