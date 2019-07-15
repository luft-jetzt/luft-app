<?php declare(strict_types=1);

namespace App\Twitter\MessageFactory;

use App\Air\ViewModel\MeasurementViewModel;

class PlainMessageFactory extends AbstractMessageFactory
{
    public function compose(): MessageFactoryInterface
    {
        $this->message .= sprintf("%s\n", $this->title);

        /** @var MeasurementViewModel $measurementViewModel */
        foreach ($this->boxList as $measurementViewModel) {
            $this->message .= sprintf("%s: %.0f %s \n", $measurementViewModel->getMeasurement()->getName(), $measurementViewModel->getData()->getValue(), $measurementViewModel->getMeasurement()->getUnitPlain());
        }

        $this->message .= sprintf("%s", $this->link);

        return $this;
    }
}
