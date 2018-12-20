<?php declare(strict_types=1);

namespace App\Analysis\FireworksAnalylsis;

use App\Pollution\Pollutant\PollutantInterface;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

abstract class AbstractFireworksAnalysis implements FireworksAnalysisInterface
{
    /** @var float $minSlope */
    protected $minSlope = 50.0;

    /** @var float $maxSlope */
    protected $maxSlope = 300.0;

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
    }

    public function setMinSlope(float $minSlope): FireworksAnalysisInterface
    {
        $this->minSlope = $minSlope;

        return $this;
    }

    public function setPollutant(PollutantInterface $pollutant): FireworksAnalysisInterface
    {
        $this->pollutant = $pollutant;

        return $this;
    }

    public function setFromDateTime(\DateTimeInterface $fromDateTime): FireworksAnalysisInterface
    {
        $this->fromDateTime = $fromDateTime;

        return $this;
    }

    public function setUntilDateTime(\DateTimeInterface $untilDateTime): FireworksAnalysisInterface
    {
        $this->untilDateTime = $untilDateTime;

        return $this;
    }

    public function setMaxSlope(float $maxSlope): FireworksAnalysisInterface
    {
        $this->maxSlope = $maxSlope;

        return $this;
    }
}
