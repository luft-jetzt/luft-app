<?php declare(strict_types=1);

namespace App\Analysis\KomfortofenAnalysis;

use App\Air\Measurement\MeasurementInterface;
use App\Pollution\DataFinder\FinderInterface;

abstract class AbstractKomfortofenAnalysis implements KomfortofenAnalysisInterface
{
    protected float $minSlope = 1.0;

    protected float $maxSlope = 300.0;

    protected MeasurementInterface $pollutant;

    protected FinderInterface $finder;

    protected \DateTimeInterface $fromDateTime;

    protected \DateTimeInterface $untilDateTime;

    protected KomfortofenModelFactoryInterface $komfortofenModelFactory;

    public function __construct(FinderInterface $finder, KomfortofenModelFactoryInterface $komfortofenModelFactory)
    {
        $this->finder = $finder;
        $this->komfortofenModelFactory = $komfortofenModelFactory;
    }

    public function setMinSlope(float $minSlope): KomfortofenAnalysisInterface
    {
        $this->minSlope = $minSlope;

        return $this;
    }

    public function setPollutant(MeasurementInterface $pollutant): KomfortofenAnalysisInterface
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
