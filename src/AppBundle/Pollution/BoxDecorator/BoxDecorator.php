<?php

namespace AppBundle\Pollution\BoxDecorator;

use AppBundle\Pollution\Box\Box;

class BoxDecorator extends AbstractBoxDecorator
{
    public function decorate(): BoxDecoratorInterface
    {
        /** @var Box $box */
        foreach ($this->boxList as $box) {
            $data = $box->getData();

            $pollutant = $this->getPollutantById($data->getPollutant());
            $level = $pollutant->getPollutionLevel()->getLevel($data);

            $box
                ->setStation($data->getStation())
                ->setPollutant($pollutant)
                ->setPollutionLevel($level)
            ;
        }

        return $this;
    }
}
