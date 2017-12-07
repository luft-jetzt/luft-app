<?php

namespace AppBundle\Pollution\DataList;

use AppBundle\Entity\Data;
use AppBundle\Pollution\Pollutant\PollutantInterface;

class DataList
{
    protected $list;

    public function __construct()
    {
        $this->reset();
    }

    public function addData(Data $data, bool $overwrite = false): DataList
    {
        if ($overwrite || !$this->hasPollutant($data)) {
            $this->list[$data->getPollutant()] = $data;
        }

        return $this;
    }

    public function hasPollutant(Data $data): bool
    {
        $pollutant = $data->getPollutant();

        return $this->list[$pollutant] !== null;
    }

    public function getMissingPollutants(): array
    {
        $missingList = [];

        array_walk($this->list, function(Data $data = null, int $key) use (&$missingList) {
            if ($data === null) {
                array_push($missingList, $key);
            }
        });

        return $missingList;
    }

    public function getList(): array
    {
        return $this->list;
    }

    public function reset(): DataList
    {
        $this->list = [
            PollutantInterface::POLLUTANT_PM10 => null,
            PollutantInterface::POLLUTANT_O3 => null,
            PollutantInterface::POLLUTANT_NO2 => null,
            PollutantInterface::POLLUTANT_SO2 => null,
            PollutantInterface::POLLUTANT_CO => null,
        ];

        return $this;
    }
}
