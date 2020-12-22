<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever\ElasticFinder;

use Elastica\Query;
use Elastica\SearchableInterface;
use FOS\ElasticaBundle\Finder\FinderInterface;

class Finder implements FinderInterface
{
    protected SearchableInterface $searchable;

    public function __construct(SearchableInterface $searchable)
    {
        $this->searchable = $searchable;
    }

    public function find($query, $limit = null, $options = []): array
    {
        $queryObject = Query::create($query);
        if (null !== $limit) {
            $queryObject->setSize($limit);
        }
        $results = $this->searchable->search($queryObject, $options)->getResults();

        return $results;
    }
}
