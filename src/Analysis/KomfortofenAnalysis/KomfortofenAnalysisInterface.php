<?php declare(strict_types=1);

namespace App\Analysis\KomfortofenAnalysis;

use App\Pollution\Pollutant\PollutantInterface;

interface KomfortofenAnalysisInterface
{
    public function setMinSlope(float $minSlope): KomfortofenAnalysisInterface;
    public function setMaxSlope(float $maxSlope): KomfortofenAnalysisInterface;
    public function setPollutant(PollutantInterface $pollutant): KomfortofenAnalysisInterface;
    public function setFromDateTime(\DateTimeInterface $fromDateTime): KomfortofenAnalysisInterface;
    public function setUntilDateTime(\DateTimeInterface $untilDateTime): KomfortofenAnalysisInterface;
    public function analyze(): array;
}
