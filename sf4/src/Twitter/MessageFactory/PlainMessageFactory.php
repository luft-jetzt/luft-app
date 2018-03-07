<?php declare(strict_types=1);

namespace App\Twitter\MessageFactory;

use App\Pollution\Box\Box;

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
