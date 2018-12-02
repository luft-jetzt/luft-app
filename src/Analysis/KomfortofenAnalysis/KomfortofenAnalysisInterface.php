<?php declare(strict_types=1);

namespace App\Analysis\KomfortofenAnalysis;

use App\Entity\Station;
use App\Pollution\Pollutant\PollutantInterface;

interface KomfortofenAnalysisInterface
{
    public function setStation(Station $station): KomfortofenAnalysisInterface;
    public function setPollutant(PollutantInterface $pollutant): KomfortofenAnalysisInterface;
    public function setFromDateTime(\DateTimeInterface $fromDateTime): KomfortofenAnalysisInterface;
    public function setUntilDateTime(\DateTimeInterface $untilDateTime): KomfortofenAnalysisInterface;
    public function analyze(): array;
}
