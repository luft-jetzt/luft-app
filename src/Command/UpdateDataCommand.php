<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\Station;
use Doctrine\Persistence\ManagerRegistry;
use Elastica\Client;
use Elastica\Request;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'luft:update-data-station',
    description: 'Update stored values with new station pin'
)]
class UpdateDataCommand extends Command
{
    public function __construct(protected ManagerRegistry $managerRegistry, protected Client $client)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('station-code', InputArgument::REQUIRED, 'Station Code to update');
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

        $io->progressStart(is_countable($hits) ? count($hits) : 0);

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

        //return Command::SUCCESS;
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
