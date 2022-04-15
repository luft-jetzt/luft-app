<?php declare(strict_types=1);

namespace App\Twitter\MessageFactory;

use App\Air\ViewModel\MeasurementViewModel;
use Symfony\Component\Translation\TranslatorInterface;

class ExtendedEmojiMessageFactory extends AbstractExtendedMessageFactory
{
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function compose(): MessageFactoryInterface
    {
        $this->message .= sprintf("%s\n", $this->title);

        /** @var array $pollutant */
        foreach ($this->pollutantList as $pollutant) {
            /** @var MeasurementViewModel $measurementViewModel */
            foreach ($pollutant as $measurementViewModel) {
                $this->message .= sprintf("%s %s: %.0f %s \n",
                    $this->getEmoji($measurementViewModel),
                    $measurementViewModel->getMeasurement()->getName(),
                    $measurementViewModel->getData()->getValue(),
                    $measurementViewModel->getMeasurement()->getUnitPlain()
                );
            }
        }

        if (0 !== count($this->additionalPollutantList)) {
            $this->message .= "\n".'zusätzliche Messwerte aus der Umgebung:'."\n";

            /** @var array $pollutant */
            foreach ($this->additionalPollutantList as $pollutant) {
                /** @var MeasurementViewModel $measurementViewModel */
                foreach ($pollutant as $measurementViewModel) {
                    $this->message .= sprintf("%s %s: %.0f %s \n", $this->getEmoji($measurementViewModel), $measurementViewModel->getMeasurement()->getName(), $measurementViewModel->getData()->getValue(), $measurementViewModel->getMeasurement()->getUnitPlain());
                }
            }

            $this->message .= "\n";
        }

        $this->message .= sprintf("%s", $this->link);

        return $this;
    }

    protected function getEmoji(MeasurementViewModel $measurementViewModel): string
    {
        $translationKey = sprintf('air_quality.index.%d.icon', $measurementViewModel->getPollutionLevel());

        return $this->translator->trans($translationKey);
    }
}
