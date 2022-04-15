<?php declare(strict_types=1);

namespace App\Twitter;

use App\Air\ViewModel\MeasurementViewModel;
use App\Entity\Station;
use App\Entity\TwitterSchedule;
use Caldera\GeoBasic\Coordinate\Coordinate;
use Cron\CronExpression;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Twitter extends AbstractTwitter
{
    public function tweet(): void
    {
        $twitterSchedules = $this->doctrine->getRepository(TwitterSchedule::class)->findAll();

        $cb = $this->getCodeBird();

        /** @var TwitterSchedule $twitterSchedule */
        foreach ($twitterSchedules as $twitterSchedule) {
            if (!$twitterSchedule->getStation() && !$twitterSchedule->getLatitude() && !$twitterSchedule->getLongitude()) {
                continue;
            }

            $cron = CronExpression::factory($twitterSchedule->getCron());

            if ($cron->isDue($this->dateTime) || $this->dryRun) {
                $user = $twitterSchedule->getCity()->getUser();

                if (!$user) {
                    continue;
                }

                $cb->setToken($user->getTwitterAccessToken(), $user->getTwitterSecret());

                $coord = $this->getCoord($twitterSchedule);

                $pollutantList = $this
                    ->pollutionDataFactory
                    ->setCoord($coord)
                    ->createDecoratedPollutantList($this->dateTime, new \DateInterval('PT3H'));

                if (0 === count($pollutantList)) {
                    continue;
                }

                $additionalCoord = new Coordinate($coord->getLatitude(), $coord->getLongitude());
                $additionalPollutantList = $this
                    ->pollutionDataFactory
                    ->setCoord($additionalCoord)
                    ->createDecoratedPollutantList($this->dateTime, new \DateInterval('PT3H'));

                foreach ($pollutantList as $pollutantId => $pollutant) {
                    if (array_key_exists($pollutantId, $additionalPollutantList)) {
                        unset($additionalPollutantList[$pollutantId]);
                    }
                }

                $message = $this->createMessage($twitterSchedule, $this->removeNotTwitterableMeasurements($pollutantList), $this->removeNotTwitterableMeasurements($additionalPollutantList));

                $params = [
                    'status' => $message,
                    'lat' => $coord->getLatitude(),
                    'long' => $coord->getLongitude(),
                ];

                if (!$this->dryRun) {
                    $reply = $cb->statuses_update($params);

                    $this->logger->notice(json_encode($reply));
                }

                $this->validScheduleList[] = $twitterSchedule;
            }
        }
    }

    protected function createMessage(TwitterSchedule $twitterSchedule, array $pollutantList, array $additionalPollutantList): string
    {
        $this->messageFactory
            ->reset()
            ->setTitle($twitterSchedule->getTitle())
            ->setPollutantList($pollutantList)
            ->setAdditionalPollutantList($additionalPollutantList);

        if ($this->dryRun) {
            $this->messageFactory->setLink('https://localhost/foobarbaz');
        } else {
            $this->messageFactory->setLink($this->createPermaLinkForTweet($twitterSchedule));
        }

        $message = $this->messageFactory
            ->compose()
            ->getMessage();

        return $message;
    }

    protected function removeNotTwitterableMeasurements(array $list): array
    {
        foreach ($list as $key => $measurementViewModelList) {
            /** @var MeasurementViewModel $measurementViewModel */
            foreach ($measurementViewModelList as $measurementViewModel) {
                if (!$measurementViewModel->getMeasurement()->includeInTweets()) {
                    unset($list[$key]);
                }
            }
        }

        return $list;
    }

    protected function createPermaLinkForTweet(TwitterSchedule $twitterSchedule): string
    {
        $dateTime = new \DateTime();

        if ($twitterSchedule->getStation()) {
            $url = $this->generateUrlForStation($twitterSchedule->getStation(), $dateTime);
        } else {
            $coord = new Coordinate($twitterSchedule->getLatitude(), $twitterSchedule->getLongitude());

            $url = $this->generateUrlForCoord($coord, $dateTime);
        }

        return $url;
    }

    protected function generateUrlForStation(Station $station, \DateTimeInterface $dateTime): string
    {
        $url = $this->router->generate('station', ['stationCode' => $station->getStationCode(), 'timestamp' => $dateTime->format('U')], UrlGeneratorInterface::ABSOLUTE_URL);

        $url = str_replace('http://', 'https://', $url);

        return $url;
    }

    protected function generateUrlForCoord(Coordinate $coord, \DateTimeInterface $dateTime): string
    {
        $url = $this->router->generate('display', ['latitude' => $coord->getLatitude(), 'longitude' => $coord->getLongitude(), 'timestamp' => $dateTime->format('U')], UrlGeneratorInterface::ABSOLUTE_URL);

        $url = str_replace('http://', 'https://', $url);

        return $url;
    }
}
