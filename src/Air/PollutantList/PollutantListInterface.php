<?php declare(strict_types=1);

namespace App\Air\PollutantList;

use App\Air\Pollutant\PollutantInterface;

interface PollutantListInterface
{
    public function addMeasurement(PollutantInterface $measurement): PollutantListInterface;
    public function getMeasurements(): array;
    public function getMeasurementWithIds(): array;
}
