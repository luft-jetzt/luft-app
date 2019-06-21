<?php declare(strict_types=1);

namespace App\Pollution\ViewModelFactory;

use App\Entity\Station;
use App\Pollution\Box\Box;

class ViewModelFactory extends AbstractViewModelFactory
{
    public function decorate(): ViewModelFactoryInterface
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
                    ->setDistance($this->calculateDistance($data->getStation()));
            }
        }

        $this->airQualityCalculator->calculatePollutantList($this->pollutantList);

        return $this;
    }

    protected function calculateDistance(Station $station): ?float
    {
        $geotools = new \League\Geotools\Geotools();

        if (!$this->coord) {
            return null;
        }

        $coordA = new \League\Geotools\Coordinate\Coordinate($this->coord->toArray());
        $coordB = new \League\Geotools\Coordinate\Coordinate($station->toArray());

        $distance = $geotools->distance()->setFrom($coordA)->setTo($coordB);

        return $distance->in('km')->haversine();
    }
}
