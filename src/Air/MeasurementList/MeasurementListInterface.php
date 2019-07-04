<?php declare(strict_types=1);

namespace App\Air\MeasurementList;

use App\Air\Measurement\MeasurementInterface;

interface MeasurementListInterface
{
    public function addMeasurement(MeasurementInterface $measurement): MeasurementListInterface;
    public function getMeasurements(): array;
    public function getMeasurementWithIds(): array;
}
