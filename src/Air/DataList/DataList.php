<?php declare(strict_types=1);

namespace App\Air\DataList;

use App\Air\Pollutant\PollutantInterface;
use App\Air\UniqueStrategy\Hasher;
use App\Entity\Data;

class DataList implements DataListInterface
{
    protected array $list = [];

    public function __construct()
    {
        $this->reset();
    }

    #[\Override]
    public function addData(Data $data): DataListInterface
    {
        $this->list[$data->getPollutant()][Hasher::hashData($data)] = $data;

        return $this;
    }

    #[\Override]
    public function hasPollutant(Data $data): bool
    {
        $pollutant = $data->getPollutant();

        return 0 !== (is_countable($this->list[$pollutant]) ? count($this->list[$pollutant]) : 0);
    }

    #[\Override]
    public function countPollutant(int $pollutant): int
    {
        return is_countable($this->list[$pollutant]) ? count($this->list[$pollutant]) : 0;
    }

    #[\Override]
    public function getList(): array
    {
        return $this->list;
    }

    #[\Override]
    public function reset(): DataListInterface
    {
        $this->list = [
            PollutantInterface::POLLUTANT_PM25 => [],
            PollutantInterface::POLLUTANT_PM10 => [],
            PollutantInterface::POLLUTANT_O3 => [],
            PollutantInterface::POLLUTANT_NO2 => [],
            PollutantInterface::POLLUTANT_SO2 => [],
            PollutantInterface::POLLUTANT_CO => [],
            PollutantInterface::POLLUTANT_CO2 => [],
            PollutantInterface::POLLUTANT_UVINDEX => [],
            PollutantInterface::POLLUTANT_TEMPERATURE => [],
            PollutantInterface::POLLUTANT_UVINDEXMAX => [],
        ];

        return $this;
    }
}
