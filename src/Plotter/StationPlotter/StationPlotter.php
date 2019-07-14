<?php declare(strict_types=1);

namespace App\Plotter\StationPlotter;

use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;
use App\Air\ViewModel\MeasurementViewModel;

class StationPlotter extends AbstractStationPlotter
{
    public function plot(string $filename): void
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
            foreach ($dataList as $pollutantId => $measurementViewModelList) {
                /** @var MeasurementViewModel $measurementViewModel */
                foreach ($measurementViewModelList as $measurementViewModel) {
                    if (!array_key_exists($pollutantId, $plotData)) {
                        $plotData[$pollutantId] = [];
                    }

                    $value = $measurementViewModel->getData()->getValue();

                    if ($value > $maxValue) {
                        $maxValue = $value;
                    }

                    array_unshift($plotData[$pollutantId], $value);

                    $dateTime = $measurementViewModel->getData()->getDateTime();

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
            $linePlot->SetColor($this->getColorForPollutantId($pollutantId));
            $linePlot->SetLegend($this->measurementList->getMeasurementWithIds()[$pollutantId]->getName());

            $graph->Add($linePlot);
        }

        $graph->SetScale('intlin', 0, ceil($maxValue * 1.1), 0, $maxDataListLength);
        $graph->xaxis->SetTickPositions($tickPositions, $tickPositions, $tickLabels);

        $graph->Stroke(_IMG_HANDLER);
        $graph->img->Stream($filename);
    }
}
