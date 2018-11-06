<?php declare(strict_types=1);

namespace App\Twitter\MessageFactory;

use App\Pollution\Box\Box;
use App\Pollution\PollutionLevel\PollutionLevel;

class EmojiMessageFactory extends AbstractMessageFactory
{
    public function compose(): MessageFactoryInterface
    {
        $this->message .= sprintf("%s\n", $this->title);

        /** @var array $pollutant */
        foreach ($this->pollutantList as $pollutant) {
            /** @var Box $box */
            foreach ($pollutant as $box) {
                $this->message .= sprintf("%s %s: %.0f %s \n", $this->getEmoji($box), $box->getPollutant()->getName(), $box->getData()->getValue(), $box->getPollutant()->getUnitPlain());
            }
        }

        $this->message .= sprintf("%s", $this->link);

        return $this;
    }

    protected function getEmoji(Box $box): string
    {
        $level = $box->getPollutionLevel();

        switch ($level) {
            case PollutionLevel::LEVEL_ACCEPTABLE:
                return '✅';
            case PollutionLevel::LEVEL_WARNING:
                return '⚠';
            case PollutionLevel::LEVEL_DANGER:
                return '❌';
            case PollutionLevel::LEVEL_DEATH:
                return '☠️';
            default:
                return '';
        }
    }
}
