<?php declare(strict_types=1);

namespace App\Analysis\LimitAnalysis;

use App\Air\Measurement\MeasurementInterface;
use App\Entity\Station;

interface LimitAnalysisInterface
{
    public function setStation(Station $station): LimitAnalysisInterface;
    public function setMeasurement(MeasurementInterface $measurement): LimitAnalysisInterface;
    public function setFromDateTime(\DateTimeInterface $fromDateTime): LimitAnalysisInterface;
    public function setUntilDateTime(\DateTimeInterface $untilDateTime): LimitAnalysisInterface;
    public function analyze(): array;
}
