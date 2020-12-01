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

    public function purgeData(\DateTimeInterface $untilDateTime, ProviderInterface $provider = null): int
    {
        $counter = $this->purgeDatabase($untilDateTime, $provider);

        $this->purgeElasticsearch($untilDateTime, $provider);

        return $counter;
    }

    public function purgeDatabase(\DateTimeInterface $untilDateTime, ProviderInterface $provider = null): int
    {
        return $this->managerRegistry->getRepository(Data::class)->deleteData($untilDateTime, $provider);
    }

    public function purgeElasticsearch(\DateTimeInterface $untilDateTime, ProviderInterface $provider = null): int
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

        $this->client->request('air_data/_delete_by_query', Request::POST, $query);

        return 0;
    }
}
