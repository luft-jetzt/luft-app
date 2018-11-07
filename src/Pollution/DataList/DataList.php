<?php declare(strict_types=1);

namespace App\Pollution\DataList;

use App\Entity\Data;
use App\Pollution\Pollutant\PollutantInterface;

class DataList implements DataListInterface
{
    /** @var array $list */
    protected $list = [];

    public function __construct()
    {
        $this->reset();
    }

    public function addData(Data $data): DataListInterface
    {
        $this->list[$data->getPollutant()][$data->getId()] = $data;

        return $this;
    }

    public function hasPollutant(Data $data): bool
    {
        $pollutant = $data->getPollutant();

        return 0 !== count($this->list[$pollutant]);
    }

    public function countPollutant(int $pollutant): int
    {
        return count($this->list[$pollutant]);
    }

    public function getList(): array
    {
        return $this->list;
    }

    public function reset(): DataListInterface
    {
        $this->list = [
            PollutantInterface::POLLUTANT_PM10 => [],
            PollutantInterface::POLLUTANT_PM25 => [],
            PollutantInterface::POLLUTANT_O3 => [],
            PollutantInterface::POLLUTANT_NO2 => [],
            PollutantInterface::POLLUTANT_SO2 => [],
            PollutantInterface::POLLUTANT_CO => [],
        ];

        return $this;
    }
}
