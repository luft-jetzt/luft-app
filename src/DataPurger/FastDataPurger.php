<?php declare(strict_types=1);

namespace App\DataPurger;

use App\Entity\Data;
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

    public function purgeData(\DateTime $untilDateTime, ProviderInterface $provider = null, bool $withTags): int
    {
        $counter = $this->purgeDatabase($untilDateTime, $provider, $withTags);

        $this->purgeElasticsearch($untilDateTime, $provider, $withTags);

        return $counter;
    }

    public function purgeDatabase(\DateTime $untilDateTime, ProviderInterface $provider = null, bool $withTags): int
    {
        return $this->managerRegistry->getRepository(Data::class)->deleteData($untilDateTime, $provider, $withTags);
    }

    public function purgeElasticsearch(\DateTime $untilDateTime, ProviderInterface $provider = null, bool $withTags): int
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

        $this->client->request('air_data/_delete_by_query', Request::POST, $query);

        return 0;
    }
}
