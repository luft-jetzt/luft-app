<?php

namespace AppBundle\Twitter\MessageFactory;

use AppBundle\Pollution\Box\Box;
use AppBundle\Pollution\Pollutant\PollutantInterface;
use AppBundle\Pollution\PollutionLevel\PollutionLevel;

class EmojiMessageFactory extends AbstractMessageFactory
{
    public function compose(): MessageFactoryInterface
    {
        $this->message = sprintf("%s\n", $this->title);

        /** @var Box $box */
        foreach ($this->boxList as $box) {
            $this->message .= sprintf("%s %s: %.0f %s \n", $this->getEmoji($box), $box->getPollutant()->getName(), $box->getData()->getValue(), $box->getPollutant()->getUnitPlain());
        }

        return $this;
    }

    protected function getEmoji(Box $box): string
    {
        $level = $box->getPollutionLevel();

        switch ($level) {
            case PollutionLevel::LEVEL_ACCEPTABLE:
                return '✅';
            case PollutionLevel::LEVEL_WARNING:
                return '💀';
            case PollutionLevel::LEVEL_DANGER:
                return '❌';
            case PollutionLevel::LEVEL_DEATH:
                return '⚠';
            default:
                return '';
        }
    }
}
