<?php declare(strict_types=1);

namespace App\DataPurger;

use App\Provider\ProviderInterface;
use Doctrine\Persistence\ManagerRegistry;
use Elastica\Client;
use Elastica\Request;

class FastDataPurger implements DataPurgerInterface
{
    public function __construct(protected ManagerRegistry $managerRegistry, protected Client $client)
    {
    }

    public function purge(\DateTime $untilDateTime, bool $withTags, ProviderInterface $provider = null): int
    {
        $counter = $this->countData($untilDateTime, $withTags, $provider);
        $this->purgeData($untilDateTime, $withTags, $provider);

        return $counter;
    }

    public function countData(\DateTime $untilDateTime, bool $withTags, ProviderInterface $provider = null): int
    {
        $query = $this->buildQuery($untilDateTime, $withTags, $provider);

        $result = $this->client->request('air_data/_count', Request::GET, $query);

        $response = $result->getData();

        $count = $response['count'];

        return $count;
    }

    public function purgeData(\DateTime $untilDateTime, bool $withTags, ProviderInterface $provider = null): void
    {
        $query = $this->buildQuery($untilDateTime, $withTags, $provider);

        $this->client->request('air_data/_delete_by_query', Request::POST, $query);
    }

    protected function buildQuery(\DateTime $untilDateTime, bool $withTags, ProviderInterface $provider = null): array
    {
        $query = [
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'range' => [
                                'dateTime' =>
                                    [
                                        'lt' => $untilDateTime->format('Y-m-d H:i:s'),
                                    ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        if ($provider) {
            $query['query']['bool']['must'][]['match'] = [
                'provider' => $provider->getIdentifier(),
            ];
        }

        if (!$withTags) {
            $query['query']['bool']['must_not'][]['exists'] = ['field' => 'tag'];
        }

        return $query;
    }
}
