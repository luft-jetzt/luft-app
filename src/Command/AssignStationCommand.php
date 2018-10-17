<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\City;
use App\Entity\Station;
use Doctrine\ORM\EntityManagerInterface;
use Geocoder\Query\ReverseQuery;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class AssignStationCommand extends Command
{
    const LOCALE = 'de';
    const NOMINATIM_URL = 'https://nominatim.openstreetmap.org';
    const USER_AGENT = 'Luft.jetzt geocoder';
    const REFERER = 'https://luft.jetzt/';

    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(?string $name = null, RegistryInterface $registry)
    {
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setName('luft:assign-station')
            ->setDescription('Assign station to city');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $stationList = $this->registry->getRepository(Station::class)->findWithoutCity();

        /** @var Station $station */
        foreach ($stationList as $station) {
            $output->writeln(sprintf('Station <info>%s</info>: <comment>%s</comment>', $station->getStationCode(), $station->getTitle()));

            $cityName = $this->determineCityName($station);

            $output->writeln(sprintf('Proposed city by coords <info>%f</info>, <info>%f</info>: <comment>%s</comment>', $station->getLatitude(), $station->getLongitude(), $cityName));

            $helper = $this->getHelper('question');

            if ($cityName && $city = $this->getCityByName($cityName)) {
                $question = new ConfirmationQuestion(sprintf('Assign station to existing city <comment>%s</comment>?', $city->getName()), false);

                if ($helper->ask($input, $output, $question)) {
                    $station->setCity($city);
                }
            } else {
                $question = new ConfirmationQuestion(sprintf('Create new city <comment>%s</comment> and assign station <info>%s</info>?', $cityName, $station->getStationCode()), false);

                if ($helper->ask($input, $output, $question)) {
                    $citySlug = strtolower($cityName);
                    $citySlug = str_replace(' ', '-', $citySlug);

                    $citySlugQuestion = new Question(sprintf('Please propose a city slug for new city <comment>%s</comment>: <comment>[%s]</comment> ', $cityName, $citySlug), $citySlug);

                    $citySlug = $helper->ask($input, $output, $citySlugQuestion);

                    $city = new City();
                    $city->setName($cityName)
                        ->setCreatedAt(new \DateTime())
                        ->setSlug($citySlug);

                    $this->registry->getManager()->persist($city);

                    $station->setCity($city);
                }
            }

            $this->registry->getManager()->flush();
        }
    }

    protected function determineCityName(Station $station): ?string
    {
        $httpClient = new \Http\Adapter\Guzzle6\Client();
        $provider = new \Geocoder\Provider\Nominatim\Nominatim($httpClient, self::NOMINATIM_URL, self::USER_AGENT, self::REFERER);
        $geocoder = new \Geocoder\StatefulGeocoder($provider, self::LOCALE);

        $result = $geocoder->reverseQuery(ReverseQuery::fromCoordinates($station->getLatitude(), $station->getLongitude()));

        $cityName = $result->first()->getLocality();

        return $cityName;
    }

    protected function getCityByName(string $cityName): ?City
    {
        return $this->registry->getRepository(City::class)->findOneByName($cityName);
    }
}
