<?php declare(strict_types=1);

namespace App\Analysis\CoronaFireworksAnalysis\Twig;

use App\Analysis\CoronaFireworksAnalysis\StartDateTimeCalculator;
use Carbon\Carbon;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CoronaFireworksTwigExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('corona_fireworks_datetime', [$this, 'coronaFireworksDateTime']),
        ];
    }

    public function coronaFireworksDateTime(string $minutesSinceStartDateTime): Carbon
    {
        $startDateTime = StartDateTimeCalculator::calculateStartDateTime(2020);
        return $startDateTime->addMinutes((int) $minutesSinceStartDateTime);
    }
}
