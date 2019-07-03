<?php declare(strict_types=1);

namespace App\Air\MeasurementList;

use App\Air\Measurement\MeasurementInterface;

class MeasurementList implements MeasurementListInterface
{
    /** @var array $list */
    protected $list;

    public function addMeasurement(MeasurementInterface $measurement): MeasurementListInterface
    {
        $this->list[$measurement->getIdentifier()] = $measurement;

        return $this;
    }

    public function getMeasurements(): array
    {
        return $this->list;
    }

    public function getMeasurementWithIds(): array
    {
        $measurementListWithIds = [];

        /** @var MeasurementInterface $measurement */
        foreach ($this->list as $measurement) {
            $measurementId = constant(sprintf('App\\Pollution\\Pollutant\\PollutantInterface::POLLUTANT_%s', strtoupper($measurement->getIdentifier())));

            $measurementListWithIds[$measurementId] = $measurement;
        }

        return $measurementListWithIds;
    }
}
