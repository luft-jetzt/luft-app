<?php declare(strict_types=1);

namespace App\Command;

use App\Air\Geocoding\Guesser\CityGuesserInterface;
use App\Entity\City;
use App\Entity\Station;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'luft:assign-station',
    description: 'Assign station to city'
)]
class AssignStationCommand extends Command
{
    public function __construct(protected ManagerRegistry $registry, protected CityGuesserInterface $cityGuesser)
    {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stationList = $this->registry->getRepository(Station::class)->findWithoutCity();

        /** @var Station $station */
        foreach ($stationList as $station) {
            $output->writeln(sprintf('Station <info>%s</info>: <comment>%s</comment>', $station->getStationCode(), $station->getTitle()));

            $cityName = $this->cityGuesser->guess($station);

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
                    $citySlug = $this->generateCitySlugByCityName($cityName);

                    $citySlugQuestion = new Question(sprintf('Please propose a city slug for new city <comment>%s</comment>: <comment>[%s]</comment> ', $cityName, $citySlug), $citySlug);

                    $citySlug = $helper->ask($input, $output, $citySlugQuestion);

                    $city = $this->createCity($cityName, $citySlug);

                    $this->registry->getManager()->persist($city);

                    $station->setCity($city);
                }
            }

            $this->registry->getManager()->flush();
        }

        //return Command::SUCCESS;
        return 0;
    }

    protected function generateCitySlugByCityName(string $cityName): string
    {
        $citySlug = strtolower($cityName);
        $citySlug = str_replace(' ', '-', $citySlug);

        return $citySlug;
    }

    protected function getCityByName(string $cityName): ?City
    {
        return $this->registry->getRepository(City::class)->findOneByName($cityName);
    }

    protected function createCity(string $cityName, string $citySlug): City
    {
        $city = new City();
        $city->setName($cityName)
            ->setCreatedAt(new \DateTime())
            ->setSlug($citySlug);

        return $city;
    }
}
