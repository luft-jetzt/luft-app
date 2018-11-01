<?php declare(strict_types=1);

namespace App\Pollution\BoxDecorator;

use App\Pollution\Box\Box;

class BoxDecorator extends AbstractBoxDecorator
{
    public function decorate(): BoxDecoratorInterface
    {
        /** @var Box $box */
        foreach ($this->boxList as $box) {
            $data = $box->getData();

            $pollutant = $this->getPollutantById($data->getPollutant());

            $box
                ->setStation($data->getStation())
                ->setPollutant($pollutant);
        }

        $this->airQualityCalculator->calculateBoxList($this->boxList);

        return $this;
    }
}
