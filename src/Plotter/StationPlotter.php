<?php declare(strict_types=1);

namespace App\Plotter;

use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;
use App\Entity\Station;
use App\Pollution\Box\Box;
use App\Pollution\PollutantList\PollutantListInterface;
use App\Pollution\PollutionDataFactory\HistoryDataFactoryInterface;

class StationPlotter
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

    public function __construct(PollutantListInterface $pollutantList, HistoryDataFactoryInterface $historyDataFactory)
    {
        $this->pollutantList = $pollutantList;
        $this->historyDataFactory = $historyDataFactory;
    }

    public function setFromDateTime(\DateTime $fromDateTime): StationPlotter
    {
        $this->fromDateTime = $fromDateTime;

        return $this;
    }

    public function setUntilDateTime(\DateTime $untilDateTime): StationPlotter
    {
        $this->untilDateTime = $untilDateTime;

        return $this;
    }

    public function setStation(Station $station): StationPlotter
    {
        $this->station = $station;

        return $this;
    }

    public function plot(): void
    {
        $dataLists = $this->getDataLists();

        $graph    = new Graph\Graph(800, 400);
        $graph->title->Set(sprintf('Messwerte der Station %s', $this->station->getStationCode()));
        $graph->SetBox(true);

        $plotData = [];
        $maxValue = 0;
        $i = count($dataLists);

        /** @var array $dataList */
        foreach ($dataLists as $timestamp => $dataList) {
            foreach ($dataList as $pollutantId => $boxList) {
                /** @var Box $box */
                foreach ($boxList as $box) {
                    if (!array_key_exists($pollutantId, $plotData)) {
                        $plotData[$pollutantId] = [];
                    }

                    $value = $box->getData()->getValue();

                    if ($value > $maxValue) {
                        $maxValue = $value;
                    }

                    array_unshift($plotData[$pollutantId], $value);

                    $dateTime = $box->getData()->getDateTime();

                    $tickPositions[$i] = $i;

                    if ($dateTime->format('H') % 4 === 0) {
                        $tickLabels[$i] = $dateTime->format('H:i');
                    } else {
                        $tickLabels[$i] = null;
                    }

                    --$i;
                }
            }
        }

        $maxDataListLength = 0;

        foreach ($plotData as $pollutantId => $valueList) {
            if (count($valueList) > $maxDataListLength) {
                $maxDataListLength = count($valueList);
            }

            $linePlot   = new Plot\LinePlot($valueList);
            $linePlot->SetColor('red');
            $linePlot->SetLegend($this->pollutantList->getPollutantsWithIds()[$pollutantId]->getName());

            $graph->Add($linePlot);
        }

        $graph->SetScale('intlin', 0, ceil($maxValue * 1.1), 0, $maxDataListLength);
        $graph->xaxis->SetTickPositions($tickPositions, $tickPositions, $tickLabels);

        $graph->Stroke();
    }

    protected function getDataLists(): array
    {
        $dataLists = $this->historyDataFactory
            ->setStation($this->station)
            ->createDecoratedPollutantListForInterval($this->fromDateTime, $this->untilDateTime);

        krsort($dataLists);

        return $dataLists;
    }
}
