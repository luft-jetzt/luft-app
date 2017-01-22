<?php

namespace AppBundle\SourceFetcher\Query;

interface QueryInterface
{
    public function getQueryString(): string;
    public function getQueryOptions(): array;
}