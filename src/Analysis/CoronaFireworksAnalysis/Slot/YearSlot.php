<?php declare(strict_types=1);

namespace App\Analysis\CoronaFireworksAnalysis\Slot;

use App\Air\ViewModel\MeasurementViewModel;
use App\Entity\Data;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;

class YearSlot
{
    protected array $modelList = [];
    protected \DateTimeZone $dateTimeZone;

    public function __construct(protected int $startYear)
    {
        $this->dateTimeZone = new CarbonTimeZone('Europe/Berlin');
    }

    public function accepts(Data $data): bool
    {
        $fromDateTimeSpec = sprintf('%d-12-31 18:00:00', $this->startYear);
        $untilDateTimeSpec = sprintf('%d-01-01 06:00:00', ($this->startYear + 1));
        $fromDateTime = new Carbon($fromDateTimeSpec, $this->dateTimeZone);
        $untilDateTime = new Carbon($untilDateTimeSpec, $this->dateTimeZone);

        return ($fromDateTime < $data->getDateTime()) && ($data->getDateTime() < $untilDateTime);
    }

    public function addSlot(int $key): void
    {
        $this->modelList[$key] = null;
    }

    public function addModel(MeasurementViewModel $model): void
    {
        $fromDateTimeSpec = sprintf('%d-12-31 18:00:00', $this->startYear);
        $fromDateTime = new Carbon($fromDateTimeSpec, $this->dateTimeZone);

        $diff = $fromDateTime->diffInMinutes($model->getData()->getDateTime());

        foreach ($this->modelList as $timeSlot => $value) {
            if ($timeSlot < $diff) {
                $this->modelList[$timeSlot] = $model;

                return;
            }
        }
    }

    public function removeSlot(int $key): void
    {
        unset($this->modelList[$key]);
    }

    public function getList(): array
    {
        return $this->modelList;
    }
}
