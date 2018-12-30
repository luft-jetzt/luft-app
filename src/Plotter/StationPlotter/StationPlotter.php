<?php declare(strict_types=1);

namespace App\Plotter\StationPlotter;

use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;
use App\Pollution\Box\Box;

class StationPlotter extends AbstractStationPlotter
{
    public function plot(): void
    {
        $dataLists = $this->getDataLists();

        $graph    = new Graph\Graph($this->width, $this->height);
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
}
