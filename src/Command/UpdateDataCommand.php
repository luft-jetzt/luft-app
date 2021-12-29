<?php declare(strict_types=1);

namespace App\Command;

use Doctrine\Persistence\ManagerRegistry;
use Elastica\Client;
use Elastica\Request;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateDataCommand extends Command
{
    protected static $defaultName = 'luft:update-data-station';

    protected Client $client;
    protected ManagerRegistry $managerRegistry;

    public function __construct(string $name = null, ManagerRegistry $managerRegistry, Client $client)
    {
        $this->managerRegistry = $managerRegistry;
        $this->client = $client;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Update stored values with new station pin')
            ->addArgument('station-code', InputArgument::REQUIRED, 'Station Code to update')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $stationCode = $input->getArgument('station-code');

        $query = $this->buildQuery($stationCode);
        $result = $this->client->request('air_data/_search?size=10000', Request::GET, $query);

        $hits = $result->getData()['hits']['hits'];

        $newPin = [
            'lat' => 5,
            'lon' => 3,
        ];

        foreach ($hits as $key => $hit) {
            $id = $hit['_id'];

            $source = $hit['_source'];

            dump($source);
        }
        return 0;
    }

    protected function buildQuery(string $stationCode): array
    {
        $query = [
            'query' => [
                'term' => [
                    'stationCode' => [
                        'value' => $stationCode,
                    ]
                ]
            ]
        ];

        return $query;
    }
}
