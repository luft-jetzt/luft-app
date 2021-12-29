<?php declare(strict_types=1);

namespace App\Analysis\CoronaFireworksAnalysis\Slot;

use App\Air\ViewModel\MeasurementViewModel;
use App\Entity\Data;
use Carbon\Carbon;

class YearSlot
{
    protected int $startYear;
    protected array $modelList = [];

    public function __construct(int $startYear)
    {
        $this->startYear = $startYear;
    }

    public function accepts(Data $data): bool
    {
        $fromDateTimeSpec = sprintf('%d-12-31 12:00:00', $this->startYear);
        $untilDateTimeSpec = sprintf('%d-01-01 12:00:00', ($this->startYear + 1));
        $fromDateTime = new Carbon($fromDateTimeSpec);
        $untilDateTime = new Carbon($untilDateTimeSpec);

        return ($fromDateTime < $data->getDateTime()) && ($data->getDateTime() < $untilDateTime);
    }

    public function addSlot(int $key): void
    {
        $this->modelList[$key] = null;
    }

    public function addModel(MeasurementViewModel $model): void
    {
        $fromDateTimeSpec = sprintf('%d-12-31 12:00:00', $this->startYear);
        $fromDateTime = new Carbon($fromDateTimeSpec);

        $diff = $fromDateTime->diffInMinutes($model->getData()->getDateTime());

        foreach ($this->modelList as $timeSlot => $value) {
            if ($timeSlot < $diff) {
                $this->modelList[$timeSlot] = $model;

                return;
            }
        }
    }

    public function getList(): array
    {
        return $this->modelList;
    }
}
