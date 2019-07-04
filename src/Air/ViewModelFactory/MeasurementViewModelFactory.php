<?php declare(strict_types=1);

namespace App\Air\ViewModelFactory;

use App\Air\ViewModel\MeasurementViewModel;

class MeasurementViewModelFactory extends AbstractMeasurementViewModelFactory
{
    public function decorate(): MeasurementViewModelFactoryInterface
    {
        /** @var array $boxArray */
        foreach ($this->pollutantList as $measurementViewModelList) {
            /** @var MeasurementViewModel $measurementViewModel */
            foreach ($measurementViewModelList as $measurementViewModel) {
                $data = $measurementViewModel->getData();

                $measurement = $this->getPollutantById($data->getPollutant());

                $measurementViewModel
                    ->setStation($data->getStation())
                    ->setMeasurement($measurement)
                    ->setDistance(DistanceCalculator::distance($this->coord, $data->getStation()));
            }
        }

        $this->airQualityCalculator->calculatePollutantList($this->pollutantList);

        return $this;
    }
}
