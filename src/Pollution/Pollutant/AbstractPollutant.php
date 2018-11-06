<?php declare(strict_types=1);

namespace App\Pollution\Pollutant;

abstract class AbstractPollutant implements PollutantInterface
{
    /** @var string $unitHtml */
    protected $unitHtml;

    /** @var string $unitHtml */
    protected $unitPlain;

    /** @var string $name */
    protected $name;

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

    public function getIdentifier(): string
    {
        $reflection = new \ReflectionClass($this);
        return strtolower($reflection->getShortName());
    }
}
