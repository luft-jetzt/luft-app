<?php declare(strict_types=1);

namespace App\DataPurger;

use App\Provider\ProviderInterface;
use Doctrine\Persistence\ManagerRegistry;
use Elastica\Client;
use Elastica\Request;

class FastDataPurger implements DataPurgerInterface
{
    protected ManagerRegistry $managerRegistry;
    protected Client $client;

    public function __construct(ManagerRegistry $managerRegistry, Client $client)
    {
        $this->managerRegistry = $managerRegistry;
        $this->client = $client;
    }

    public function purge(\DateTime $untilDateTime, ProviderInterface $provider = null, bool $withTags): int
    {
        $counter = $this->countData($untilDateTime, $provider, $withTags);
        $this->purgeData($untilDateTime, $provider, $withTags);

        return $counter;
    }

    public function countData(\DateTime $untilDateTime, ProviderInterface $provider = null, bool $withTags): int
    {
        $query = $this->buildQuery($untilDateTime, $provider, $withTags);

        $result = $this->client->request('air_data/_count', Request::GET, $query);

        $response = $result->getData();

        $count = $response['count'];

        return $count;
    }

    public function purgeData(\DateTime $untilDateTime, ProviderInterface $provider = null, bool $withTags): void
    {
        $query = $this->buildQuery($untilDateTime, $provider, $withTags);

        $this->client->request('air_data/_delete_by_query', Request::POST, $query);
    }

    protected function buildQuery(\DateTime $untilDateTime, ProviderInterface $provider = null, bool $withTags): array
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
