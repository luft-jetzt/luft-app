<?php declare(strict_types=1);

namespace App\Analysis\KomfortofenAnalysis;

use App\Air\Measurement\MeasurementInterface;

interface KomfortofenAnalysisInterface
{
    public function setMinSlope(float $minSlope): KomfortofenAnalysisInterface;
    public function setMaxSlope(float $maxSlope): KomfortofenAnalysisInterface;
    public function setPollutant(MeasurementInterface $pollutant): KomfortofenAnalysisInterface;
    public function setFromDateTime(\DateTimeInterface $fromDateTime): KomfortofenAnalysisInterface;
    public function setUntilDateTime(\DateTimeInterface $untilDateTime): KomfortofenAnalysisInterface;
    public function analyze(): array;
}
