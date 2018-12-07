<?php declare(strict_types=1);

namespace App\Analysis\LimitAnalysis;

use App\Entity\Station;
use App\Pollution\Pollutant\PollutantInterface;

interface LimitAnalysisInterface
{
    public function setStation(Station $station): LimitAnalysisInterface;
    public function setPollutant(PollutantInterface $pollutant): LimitAnalysisInterface;
    public function setFromDateTime(\DateTimeInterface $fromDateTime): LimitAnalysisInterface;
    public function setUntilDateTime(\DateTimeInterface $untilDateTime): LimitAnalysisInterface;
    public function analyze(): array;
}
