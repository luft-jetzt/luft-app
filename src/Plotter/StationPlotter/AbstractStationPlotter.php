<?php declare(strict_types=1);

namespace App\Plotter\StationPlotter;

use App\Entity\Station;
use App\Pollution\Pollutant\PollutantInterface;
use App\Pollution\PollutantList\PollutantListInterface;
use App\Pollution\PollutionDataFactory\HistoryDataFactoryInterface;

abstract class AbstractStationPlotter implements StationPlotterInterface
{
    /** @var Station $station */
    protected $station;

    /** @var PollutantListInterface $pollutantList */
    protected $pollutantList;

    /** @var HistoryDataFactoryInterface $historyDataFactory */
    protected $historyDataFactory;

    /** @var \DateTime $fromDateTime */
    protected $fromDateTime;

    /** @var \DateTime $untilDateTime */
    protected $untilDateTime;

    /** @var int $width */
    protected $width;

    /** @var int $height */
    protected $height;

    /** @var string $title */
    protected $title;

    /** @var array $colorMap */
    protected $colorMap = [
        PollutantInterface::POLLUTANT_PM10 => 'red',
        PollutantInterface::POLLUTANT_PM25 => 'orange',
        PollutantInterface::POLLUTANT_O3 => 'green',
        PollutantInterface::POLLUTANT_NO2 => 'blue',
        PollutantInterface::POLLUTANT_SO2 => 'yellow',
        PollutantInterface::POLLUTANT_CO => 'back',
    ];
    
    public function __construct(PollutantListInterface $pollutantList, HistoryDataFactoryInterface $historyDataFactory)
    {
        $this->pollutantList = $pollutantList;
        $this->historyDataFactory = $historyDataFactory;
    }

    public function setFromDateTime(\DateTime $fromDateTime): StationPlotterInterface
    {
        $this->fromDateTime = $fromDateTime;

        return $this;
    }

    public function setUntilDateTime(\DateTime $untilDateTime): StationPlotterInterface
    {
        $this->untilDateTime = $untilDateTime;

        return $this;
    }

    public function setStation(Station $station): StationPlotterInterface
    {
        $this->station = $station;

        return $this;
    }

    public function setWidth(int $width): StationPlotterInterface
    {
        $this->width = $width;

        return $this;
    }

    public function setHeight(int $height): StationPlotterInterface
    {
        $this->height = $height;

        return $this;
    }

    public function setTitle(string $title): StationPlotterInterface
    {
        $this->title = $title;

        return $this;
    }

    protected function getDataLists(): array
    {
        $dataLists = $this->historyDataFactory
            ->setStation($this->station)
            ->createDecoratedPollutantListForInterval($this->fromDateTime, $this->untilDateTime);

        krsort($dataLists);

        return $dataLists;
    }

    protected function getColorForPollutantId(int $pollutantId): string
    {
        return $this->colorMap[$pollutantId];
    }
}
