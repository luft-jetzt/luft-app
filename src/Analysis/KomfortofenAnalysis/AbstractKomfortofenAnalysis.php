<?php declare(strict_types=1);

namespace App\Analysis\KomfortofenAnalysis;

use App\Pollution\Pollutant\PollutantInterface;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

abstract class AbstractKomfortofenAnalysis implements KomfortofenAnalysisInterface
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

    /** @var KomfortofenModelFactoryInterface $komfortofenModelFactory */
    protected $komfortofenModelFactory;

    public function __construct(PaginatedFinderInterface $finder, KomfortofenModelFactoryInterface $komfortofenModelFactory)
    {
        $this->finder = $finder;
        $this->komfortofenModelFactory = $komfortofenModelFactory;
    }

    public function setMinSlope(float $minSlope): KomfortofenAnalysisInterface
    {
        $this->minSlope = $minSlope;

        return $this;
    }

    public function setPollutant(PollutantInterface $pollutant): KomfortofenAnalysisInterface
    {
        $this->pollutant = $pollutant;

        return $this;
    }

    public function setFromDateTime(\DateTimeInterface $fromDateTime): KomfortofenAnalysisInterface
    {
        $this->fromDateTime = $fromDateTime;

        return $this;
    }

    public function setUntilDateTime(\DateTimeInterface $untilDateTime): KomfortofenAnalysisInterface
    {
        $this->untilDateTime = $untilDateTime;

        return $this;
    }

    public function setMaxSlope(float $maxSlope): KomfortofenAnalysisInterface
    {
        $this->maxSlope = $maxSlope;

        return $this;
    }
}
