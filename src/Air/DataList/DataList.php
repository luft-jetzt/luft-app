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
            PollutantInterface::MEASUREMENT_PM25 => [],
            PollutantInterface::MEASUREMENT_PM10 => [],
            PollutantInterface::MEASUREMENT_O3 => [],
            PollutantInterface::MEASUREMENT_NO2 => [],
            PollutantInterface::MEASUREMENT_SO2 => [],
            PollutantInterface::MEASUREMENT_CO => [],
            PollutantInterface::MEASUREMENT_CO2 => [],
            PollutantInterface::MEASUREMENT_UVINDEX => [],
            PollutantInterface::MEASUREMENT_TEMPERATURE => [],
            PollutantInterface::MEASUREMENT_UVINDEXMAX => [],
        ];

        return $this;
    }
}
