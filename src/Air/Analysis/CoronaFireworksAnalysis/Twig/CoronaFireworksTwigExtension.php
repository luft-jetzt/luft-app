<?php declare(strict_types=1);

namespace App\Air\Analysis\CoronaFireworksAnalysis\Twig;

use App\Air\Analysis\CoronaFireworksAnalysis\StartDateTimeCalculator;
use Carbon\Carbon;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CoronaFireworksTwigExtension extends AbstractExtension
{
    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('corona_fireworks_datetime', $this->coronaFireworksDateTime(...)),
        ];
    }

    public function coronaFireworksDateTime(string $minutesSinceStartDateTime): Carbon
    {
        $startDateTime = StartDateTimeCalculator::calculateStartDateTime(2021);

        return $startDateTime->addMinutes((int) $minutesSinceStartDateTime);
    }
}
