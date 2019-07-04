<?php declare(strict_types=1);

namespace App\Air\Measurement;

abstract class AbstractMeasurement implements MeasurementInterface
{
    /** @var string $unitHtml */
    protected $unitHtml;

    /** @var string $unitHtml */
    protected $unitPlain;

    /** @var string $name */
    protected $name;

    /** @var string $shortNameHtml */
    protected $shortNameHtml;

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
}
