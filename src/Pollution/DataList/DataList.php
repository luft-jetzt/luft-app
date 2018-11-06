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
            PollutantInterface::POLLUTANT_PM10 => null,
            PollutantInterface::POLLUTANT_PM25 => null,
            PollutantInterface::POLLUTANT_O3 => null,
            PollutantInterface::POLLUTANT_NO2 => null,
            PollutantInterface::POLLUTANT_SO2 => null,
            PollutantInterface::POLLUTANT_CO => null,
        ];

        return $this;
    }
}
