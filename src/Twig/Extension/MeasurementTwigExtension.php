<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Air\Pollutant\PollutantInterface;
use App\Air\PollutantList\PollutantListInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MeasurementTwigExtension extends AbstractExtension
{
    public function __construct(protected PollutantListInterface $measurementList)
    {
    }

    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('pollutant_list', $this->measurementList(...), ['is_safe' => ['raw']]),
            new TwigFunction('measurement_list', $this->measurementList(...), ['is_safe' => ['raw']]),
            new TwigFunction('pollutant_by_id', $this->measurementById(...), ['is_safe' => ['raw']]),
            new TwigFunction('measurement_by_id', $this->measurementById(...), ['is_safe' => ['raw']]),
        ];
    }

    public function measurementList(): array
    {
        return $this->measurementList->getMeasurementWithIds();
    }

    public function measurementById(int $measurementId): PollutantInterface
    {
        return $this->measurementList->getMeasurementWithIds()[$measurementId];
    }

    public function getName(): string
    {
        return 'measurement_extension';
    }
}

