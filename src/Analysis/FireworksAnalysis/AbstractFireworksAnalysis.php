<?php declare(strict_types=1);

namespace App\Analysis\FireworksAnalysis;

use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

abstract class AbstractFireworksAnalysis implements FireworksAnalysisInterface
{
    /** @var float $minSlope */
    protected $minSlope = 10.0;

    /** @var float $maxSlope */
    protected $maxSlope = 5000.0;

    /** @var PaginatedFinderInterface $finder */
    protected $finder;

    /** @var FireworksModelFactoryInterface $fireworksModelFactory */
    protected $fireworksModelFactory;

    public function __construct(PaginatedFinderInterface $finder, FireworksModelFactoryInterface $fireworksModelFactory)
    {
        $this->finder = $finder;
        $this->fireworksModelFactory = $fireworksModelFactory;
    }
}
