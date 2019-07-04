<?php declare(strict_types=1);

namespace App\Air\ViewModelFactory;

use App\Entity\Station;
use App\Pollution\Box\Box;

class MeasurementViewModelFactory extends AbstractMeasurementViewModelFactory
{
    public function decorate(): MeasurementViewModelFactoryInterface
    {
        /** @var array $boxArray */
        foreach ($this->pollutantList as $boxList) {
            /** @var Box $box */
            foreach ($boxList as $box) {
                $data = $box->getData();

                $pollutant = $this->getPollutantById($data->getPollutant());

                $box
                    ->setStation($data->getStation())
                    ->setPollutant($pollutant)
                    ->setDistance(DistanceCalculator::distance($this->coord, $data->getStation()));
            }
        }

        $this->airQualityCalculator->calculatePollutantList($this->pollutantList);

        return $this;
    }
}
