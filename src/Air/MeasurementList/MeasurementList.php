<?php declare(strict_types=1);

namespace App\Air\MeasurementList;

use App\Air\Measurement\MeasurementInterface;

class MeasurementList implements MeasurementListInterface
{
    protected array $list = [];

    #[\Override]
    public function addMeasurement(MeasurementInterface $measurement): MeasurementListInterface
    {
        $this->list[$measurement->getIdentifier()] = $measurement;

        return $this;
    }

    #[\Override]
    public function getMeasurements(): array
    {
        return $this->list;
    }

    #[\Override]
    public function getMeasurementWithIds(): array
    {
        $measurementListWithIds = [];

        /** @var MeasurementInterface $measurement */
        foreach ($this->list as $measurement) {
            $measurementId = constant(sprintf('App\\Air\\Measurement\\MeasurementInterface::MEASUREMENT_%s', strtoupper($measurement->getIdentifier())));

            $measurementListWithIds[$measurementId] = $measurement;
        }

        return $measurementListWithIds;
    }
}
