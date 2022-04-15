<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Query;

abstract class AbstractUbaQuery implements UbaQueryInterface
{
    protected int $component;
    protected array $scope = [];
    protected \DateTime $fromDateTime;
    protected \DateTime $untilDateTime;

    public function __construct()
    {
        $this->fromDateTime = new \DateTime();
        $this->untilDateTime = new \DateTime();
    }

    public function getComponent(): int
    {
        return $this->component;
    }

    public function getScope(): array
    {
        return $this->scope;
    }

    public function getFromDateTime(): \DateTime
    {
        return $this->fromDateTime;
    }

    public function setFromDateTime(\DateTime $fromDateTime): UbaQueryInterface
    {
        $this->fromDateTime = $fromDateTime;

        return $this;
    }

    public function getUntilDateTime(): \DateTime
    {
        return $this->untilDateTime;
    }

    public function setUntilDateTime(\DateTime $untilDateTime): UbaQueryInterface
    {
        $this->untilDateTime = $untilDateTime;

        return $this;
    }
}
