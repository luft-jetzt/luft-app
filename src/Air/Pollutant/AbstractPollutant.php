<?php declare(strict_types=1);

namespace App\Air\Pollutant;

abstract class AbstractPollutant implements PollutantInterface
{
    protected string $unitHtml;
    protected string $unitPlain;
    protected string $name;
    protected string $shortNameHtml;
    protected bool $showOnMap;
    protected bool $includeInTweets;
    protected int $decimals;

    #[\Override]
    public function getUnitHtml(): string
    {
        return $this->unitHtml;
    }

    #[\Override]
    public function getUnitPlain(): string
    {
        return $this->unitPlain;
    }

    #[\Override]
    public function getName(): string
    {
        return $this->name;
    }

    #[\Override]
    public function getShortNameHtml(): string
    {
        return $this->shortNameHtml;
    }

    public function getShortName(): string
    {
        $reflection = new \ReflectionClass($this);

        return $reflection->getShortName();
    }

    #[\Override]
    public function getIdentifier(): string
    {
        $reflection = new \ReflectionClass($this);
        return strtolower($reflection->getShortName());
    }

    #[\Override]
    public function showOnMap(): bool
    {
        return $this->showOnMap;
    }

    #[\Override]
    public function includeInTweets(): bool
    {
        return $this->includeInTweets;
    }

    public function getDecimals(): int
    {
        return $this->decimals;
    }
}
