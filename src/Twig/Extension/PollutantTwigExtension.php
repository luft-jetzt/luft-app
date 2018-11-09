<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Pollution\Pollutant\PollutantInterface;
use App\Pollution\PollutantList\PollutantListInterface;

class PollutantTwigExtension extends \Twig_Extension
{
    /** @var PollutantListInterface $pollutantList */
    protected $pollutantList;

    public function __construct(PollutantListInterface $pollutantList)
    {
        $this->pollutantList = $pollutantList;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('pollutant_list', [$this, 'pollutantList'], ['is_safe' => ['raw']]),
            new \Twig_SimpleFunction('pollutant_by_id', [$this, 'pollutantById'], ['is_safe' => ['raw']]),
        ];
    }

    public function pollutantList(): array
    {
        return $this->pollutantList->getPollutantsWithIds();
    }

    public function pollutantById(int $pollutantId): PollutantInterface
    {
        return $this->pollutantList->getPollutantsWithIds()[$pollutantId];
    }

    public function getName(): string
    {
        return 'pollutant_extension';
    }
}

