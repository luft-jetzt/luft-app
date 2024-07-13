<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Air\Pollutant\PollutantInterface;
use App\Air\PollutantList\PollutantListInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PollutantTwigExtension extends AbstractExtension
{
    public function __construct(protected PollutantListInterface $pollutantList)
    {
    }

    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('pollutant_list', $this->pollutantList(...), ['is_safe' => ['raw']]),
            new TwigFunction('measurement_list', $this->pollutantList(...), ['is_safe' => ['raw']]),
            new TwigFunction('pollutant_by_id', $this->pollutantById(...), ['is_safe' => ['raw']]),
            new TwigFunction('measurement_by_id', $this->pollutantById(...), ['is_safe' => ['raw']]),
        ];
    }

    public function pollutantList(): array
    {
        return $this->pollutantList->getPollutantListWithIds();
    }

    public function pollutantById(int $pollutantId): PollutantInterface
    {
        return $this->pollutantList->getPollutantListWithIds()[$pollutantId];
    }

    public function getName(): string
    {
        return 'measurement_extension';
    }
}

