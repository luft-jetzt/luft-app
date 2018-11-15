<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Query;

use App\Provider\UmweltbundesamtDe\SourceFetcher\Filter\FilterInterface;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Filter\NoopFilter;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Reporting\ReportingInterface;

abstract class AbstractUbaQuery implements UbaQueryInterface
{
    /** @var array $pollutant */
    protected $pollutant = [];

    /** @var array $scope */
    protected $scope = [];

    /** @var array $group */
    protected $group = ['station'];

    /** @var array $range */
    protected $range = [];

    /** @var ReportingInterface $reporting */
    protected $reporting;

    /** @var FilterInterface $filter */
    protected $filter;

    public function __construct(ReportingInterface $reporting)
    {
        $this->reporting = $reporting;

        $this->filter = new NoopFilter();

        $this
            ->calcRange()
            ->setupScope()
            ->setupPollutant()
        ;
    }

    protected function setupScope(): AbstractUbaQuery
    {
        $this->scope = [$this->reporting->getReportingIdentifier()];

        return $this;
    }

    public function setupPollutant(): AbstractUbaQuery
    {
        $reflection = new \ReflectionClass($this);
        $pollutant = $reflection->getShortName();

        $pollutant = str_replace('Uba', '', $pollutant);
        $pollutant = str_replace('Query', '', $pollutant);

        $this->pollutant = [$pollutant];

        return $this;
    }

    protected function calcRange(): AbstractUbaQuery
    {
        $this->range = [
            $this->reporting->getStartTimestamp(),
            $this->reporting->getEndTimestamp(),
        ];

        return $this;
    }

    public function getQueryOptions(): array
    {
        return [
            'pollutant' => $this->pollutant,
            'scope' => $this->scope,
            'group' => $this->group,
            'range' => $this->range
        ];
    }

    public function getQueryString(): string
    {
        $parts = [];
        $options = $this->getQueryOptions();

        foreach ($options as $key => $value) {
            $parts[] = $key . '[]='.implode(',', $value);
        }

        $queryString = implode('&', $parts);

        return $queryString;
    }

    public function getReporting(): ReportingInterface
    {
        return $this->reporting;
    }

    public function getDateTimeFormat(): string
    {
        return 'd.m.Y H:i';
    }

    public function getFilter(): FilterInterface
    {
        return $this->filter;
    }
}
