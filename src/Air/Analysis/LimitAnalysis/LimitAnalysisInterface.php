<?php declare(strict_types=1);

namespace App\Air\Analysis\LimitAnalysis;

use App\Air\Pollutant\PollutantInterface;
use App\Entity\Station;

interface LimitAnalysisInterface
{
    public function setStation(Station $station): LimitAnalysisInterface;
    public function setMeasurement(PollutantInterface $measurement): LimitAnalysisInterface;
    public function setFromDateTime(\DateTimeInterface $fromDateTime): LimitAnalysisInterface;
    public function setUntilDateTime(\DateTimeInterface $untilDateTime): LimitAnalysisInterface;
    public function analyze(): array;
}
