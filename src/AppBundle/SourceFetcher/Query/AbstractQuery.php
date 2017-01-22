<?php

namespace AppBundle\SourceFetcher\Query;

abstract class AbstractQuery implements QueryInterface
{
    protected $pollutant;

    protected $scope;

    protected $group;

    protected $range;

    public function __construct()
    {
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
}