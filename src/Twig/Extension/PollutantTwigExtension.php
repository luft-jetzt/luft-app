<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Pollution\Pollutant\CO;
use App\Pollution\Pollutant\NO2;
use App\Pollution\Pollutant\O3;
use App\Pollution\Pollutant\PM10;
use App\Pollution\Pollutant\PM25;
use App\Pollution\Pollutant\PollutantInterface;
use App\Pollution\Pollutant\SO2;

class PollutantTwigExtension extends \Twig_Extension
{
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('pollutant_list', [$this, 'pollutantList'], ['is_safe' => ['raw']]),
        ];
    }

    public function pollutantList(): array
    {
        return [
            PollutantInterface::POLLUTANT_PM10 => new PM10(),
            PollutantInterface::POLLUTANT_PM25 => new PM25(),
            PollutantInterface::POLLUTANT_O3 => new O3(),
            PollutantInterface::POLLUTANT_NO2 => new NO2(),
            PollutantInterface::POLLUTANT_SO2 => new SO2(),
            PollutantInterface::POLLUTANT_CO => new CO(),
        ];
    }

    public function getName(): string
    {
        return 'pollutant_extension';
    }
}

