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

    public function addData(Data $data, bool $overwrite = false): DataListInterface
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

    public function getList(): array
    {
        return $this->list;
    }

    public function reset(): DataListInterface
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
