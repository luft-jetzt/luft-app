<?php declare(strict_types=1);

namespace App\Twitter;

use App\Entity\TwitterSchedule;
use Cron\CronExpression;

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

            if ($cron->isDue() || $this->dryRun) {
                $user = $twitterSchedule->getCity()->getUser();

                if (!$user) {
                    continue;
                }

                $cb->setToken($user->getTwitterAccessToken(), $user->getTwitterSecret());

                $coord = $this->getCoord($twitterSchedule);

                $pollutantList = $this->pollutionDataFactory->setCoord($coord)->createDecoratedPollutantList();

                if (0 === count($pollutantList)) {
                    continue;
                }
                
                $message = $this->createMessage($twitterSchedule, $pollutantList);

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

    protected function createMessage(TwitterSchedule $twitterSchedule, array $pollutantList): string
    {
        $this->messageFactory
            ->reset()
            ->setTitle($twitterSchedule->getTitle())
            ->setPollutantList($pollutantList);

        if ($this->dryRun) {
            $this->messageFactory->setLink('https://localhost/foobarbaz');
        } else {
            $this->messageFactory->setLink($this->permalinkManager->createPermalinkForTweet($twitterSchedule));
        }

        $message = $this->messageFactory
            ->compose()
            ->getMessage();

        return $message;
    }
}
