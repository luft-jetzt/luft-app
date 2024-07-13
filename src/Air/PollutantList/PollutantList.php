<?php declare(strict_types=1);

namespace App\Air\PollutantList;

use App\Air\Pollutant\PollutantInterface;

class PollutantList implements PollutantListInterface
{
    protected array $list = [];

    #[\Override]
    public function addMeasurement(PollutantInterface $measurement): PollutantListInterface
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

        /** @var PollutantInterface $measurement */
        foreach ($this->list as $measurement) {
            $measurementId = constant(sprintf('App\\Air\\Measurement\\PollutantInterface::MEASUREMENT_%s', strtoupper($measurement->getIdentifier())));

            $measurementListWithIds[$measurementId] = $measurement;
        }

        return $measurementListWithIds;
    }
}
