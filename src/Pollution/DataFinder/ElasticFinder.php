<?php declare(strict_types=1);

namespace App\Pollution\DataFinder;

use Elastica\Query;
use Elastica\SearchableInterface;

class ElasticFinder implements FinderInterface
{
    protected SearchableInterface $searchable;

    public function __construct(SearchableInterface $searchable)
    {
        $this->searchable = $searchable;
    }

    public function find(Query $query, int $limit = null, array $options = []): array
    {
        $queryObject = Query::create($query);
        if (null !== $limit) {
            $queryObject->setSize($limit);
        }

        return $this->searchable->search($queryObject, $options)->getResults();
    }
}
