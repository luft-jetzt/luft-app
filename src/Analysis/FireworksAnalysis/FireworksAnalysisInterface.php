<?php declare(strict_types=1);

namespace App\Analysis\FireworksAnalylsis;

use App\Pollution\Pollutant\PollutantInterface;

interface FireworksAnalysisInterface
{
    public function setMinSlope(float $minSlope): FireworksAnalysisInterface;
    public function setMaxSlope(float $maxSlope): FireworksAnalysisInterface;
    public function setPollutant(PollutantInterface $pollutant): FireworksAnalysisInterface;
    public function setFromDateTime(\DateTimeInterface $fromDateTime): FireworksAnalysisInterface;
    public function setUntilDateTime(\DateTimeInterface $untilDateTime): FireworksAnalysisInterface;
    public function analyze(): array;
}
