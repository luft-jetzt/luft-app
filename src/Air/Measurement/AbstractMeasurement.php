<?php declare(strict_types=1);

namespace App\Air\Measurement;

abstract class AbstractMeasurement implements MeasurementInterface
{
    protected string $unitHtml;
    protected string $unitPlain;
    protected string $name;
    protected string $shortNameHtml;
    protected bool $showOnMap;
    protected bool $includeInTweets;
    protected int $decimals;

    public function getUnitHtml(): string
    {
        return $this->unitHtml;
    }

    public function getUnitPlain(): string
    {
        return $this->unitPlain;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortNameHtml(): string
    {
        return $this->shortNameHtml;
    }

    public function getShortName(): string
    {
        $reflection = new \ReflectionClass($this);

        return $reflection->getShortName();
    }

    public function getIdentifier(): string
    {
        $reflection = new \ReflectionClass($this);
        return strtolower($reflection->getShortName());
    }

    public function showOnMap(): bool
    {
        return $this->showOnMap;
    }

    public function includeInTweets(): bool
    {
        return $this->includeInTweets;
    }

    public function getDecimals(): int
    {
        return $this->decimals;
    }
}
