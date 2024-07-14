<?php declare(strict_types=1);

namespace App\Air\ViewModelFactory;

use App\Air\ViewModel\PollutantViewModel;

class PollutantViewModelFactory extends AbstractPollutantViewModelFactory
{
    #[\Override]
    public function decorate(): PollutantViewModelFactoryInterface
    {
        /** @var array $boxArray */
        foreach ($this->pollutantList as $pollutantViewModelList) {
            /** @var PollutantViewModel $pollutantViewModel
             */
            foreach ($pollutantViewModelList as $pollutantViewModel) {
                $data = $pollutantViewModel->getData();

                $pollutant = $this->getPollutantById($data->getPollutant());

                $pollutantViewModel
                    ->setStation($data->getStation())
                    ->setPollutant($pollutant)
                    ->setDistance(DistanceCalculator::distance($this->coord, $data->getStation()));
            }
        }

        $this->airQualityCalculator->calculatePollutantList($this->pollutantList);

        return $this;
    }
}
