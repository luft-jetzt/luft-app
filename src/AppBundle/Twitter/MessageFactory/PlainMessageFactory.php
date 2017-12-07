<?php

namespace AppBundle\Twitter\MessageFactory;

use AppBundle\Pollution\Box\Box;

class PlainMessageFactory extends AbstractMessageFactory
{
    public function compose(): MessageFactoryInterface
    {
        $this->message .= sprintf("%s\n", $this->title);

        /** @var Box $box */
        foreach ($this->boxList as $box) {
            $this->message .= sprintf("%s: %.0f %s \n", $box->getPollutant()->getName(), $box->getData()->getValue(), $box->getPollutant()->getUnitPlain());
        }

        $this->message .= sprintf("%s", $this->link);

        return $this;
    }
}
