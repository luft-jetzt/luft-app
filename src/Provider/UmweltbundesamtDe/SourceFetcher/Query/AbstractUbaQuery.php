<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Query;

use App\Provider\UmweltbundesamtDe\SourceFetcher\Filter\FilterInterface;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Filter\NoopFilter;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Reporting\ReportingInterface;

abstract class AbstractUbaQuery implements UbaQueryInterface
{
    /** @var int $component */
    protected $component;

    /** @var array $scope */
    protected $scope = [];

    /** @var \DateTime $fromDateTime */
    protected $fromDateTime;

    /** @var \DateTime $untilDateTime */
    protected $untilDateTime;

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
