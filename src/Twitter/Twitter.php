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

            if ($cron->isDue()) {
                $user = $twitterSchedule->getCity()->getUser();

                if (!$user) {
                    continue;
                }

                $cb->setToken($user->getTwitterAccessToken(), $user->getTwitterSecret());

                $coord = $this->getCoord($twitterSchedule);

                $boxList = $this->pollutionDataFactory->setCoord($coord)->createDecoratedBoxList();

                $message = $this->createMessage($twitterSchedule, $boxList);

                $params = [
                    'status' => $message,
                    'lat' => $coord->getLatitude(),
                    'long' => $coord->getLongitude(),
                ];

                $reply = $cb->statuses_update($params);
                
                $this->logger->notice(json_encode($reply));

                $this->validScheduleList[] = $twitterSchedule;
            }
        }
    }

    protected function createMessage(TwitterSchedule $twitterSchedule, array $boxList): string
    {
        $message = $this->messageFactory
            ->reset()
            ->setTitle($twitterSchedule->getTitle())
            ->setLink($this->permalinkManager->createPermalinkForTweet($twitterSchedule))
            ->setBoxList($boxList)
            ->compose()
            ->getMessage()
        ;

        return $message;
    }
}
