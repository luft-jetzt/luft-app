<?php declare(strict_types=1);

namespace App\Pollution\DataList;

use App\Air\Measurement\MeasurementInterface;
use App\Entity\Data;
use App\Pollution\UniqueStrategy\Hasher;

class DataList implements DataListInterface
{
    protected array $list = [];

    public function __construct()
    {
        $this->reset();
    }

    public function addData(Data $data): DataListInterface
    {
        $this->list[$data->getPollutant()][Hasher::hashData($data)] = $data;

        return $this;
    }

    public function hasPollutant(Data $data): bool
    {
        $pollutant = $data->getPollutant();

        return 0 !== (is_countable($this->list[$pollutant]) ? count($this->list[$pollutant]) : 0);
    }

    public function countPollutant(int $pollutant): int
    {
        return is_countable($this->list[$pollutant]) ? count($this->list[$pollutant]) : 0;
    }

    public function getList(): array
    {
        return $this->list;
    }

    public function reset(): DataListInterface
    {
        $this->list = [
            MeasurementInterface::MEASUREMENT_PM25 => [],
            MeasurementInterface::MEASUREMENT_PM10 => [],
            MeasurementInterface::MEASUREMENT_O3 => [],
            MeasurementInterface::MEASUREMENT_NO2 => [],
            MeasurementInterface::MEASUREMENT_SO2 => [],
            MeasurementInterface::MEASUREMENT_CO => [],
            MeasurementInterface::MEASUREMENT_CO2 => [],
            MeasurementInterface::MEASUREMENT_UVINDEX => [],
            MeasurementInterface::MEASUREMENT_TEMPERATURE => [],
            MeasurementInterface::MEASUREMENT_UVINDEXMAX => [],
        ];

        return $this;
    }
}
