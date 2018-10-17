<?php declare(strict_types=1);

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Geocoder\Query\ReverseQuery;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AssignStationCommand extends Command
{
    const LOCALE = 'de';
    const NOMINATIM_URL = 'https://nominatim.openstreetmap.org';
    const USER_AGENT = 'Luft.jetzt geocoder';
    const REFERER = 'https://luft.jetzt/';

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(?string $name = null, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('luft:assign-station')
            ->setDescription('Assign station to city');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = new \Http\Adapter\Guzzle6\Client();
        $provider = new \Geocoder\Provider\Nominatim\Nominatim($httpClient, self::NOMINATIM_URL, self::USER_AGENT, self::REFERER);
        $geocoder = new \Geocoder\StatefulGeocoder($provider, self::LOCALE);

        $result = $geocoder->reverseQuery(ReverseQuery::fromCoordinates(53, 10));

        var_dump($result);
    }
}
