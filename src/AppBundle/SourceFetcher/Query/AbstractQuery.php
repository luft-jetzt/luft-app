<?php declare(strict_types=1);

namespace AppBundle\SourceFetcher\Query;

use AppBundle\SourceFetcher\Reporting\ReportingInterface;

abstract class AbstractQuery implements QueryInterface
{
    protected $pollutant;

    protected $scope;

    protected $group;

    protected $range = [];

    /** @var ReportingInterface $reporting */
    protected $reporting;

    public function __construct(ReportingInterface $reporting)
    {
        $this->reporting = $reporting;

        $this->calcRange();
    }

    protected function calcRange(): AbstractQuery
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

    public function getDateTimeFormat(): string
    {
        return 'd.m.Y H:i';
    }
}
