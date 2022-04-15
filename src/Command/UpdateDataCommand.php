<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\Station;
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

        /** @var Station $station */
        $station = $this->managerRegistry->getRepository(Station::class)->findOneByStationCode($stationCode);

        $query = $this->buildQuery($stationCode);
        $result = $this->client->request('air_data/_search?size=10000', Request::GET, $query);

        $hits = $result->getData()['hits']['hits'];

        $newPin = [
            'lat' => $station->getLatitude(),
            'lon' => $station->getLongitude(),
        ];

        $io->progressStart(count($hits));

        foreach ($hits as $key => $hit) {
            $id = $hit['_id'];

            $source = $hit['_source'];

            $source['pin'] = $newPin;
            $source['station']['pin'] = $newPin;

            $path = sprintf('air_data/_doc/%s', $id);
            $result = $this->client->request($path, Request::PUT, $source);

            $io->progressAdvance();
        }

        $io->progressFinish();

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
