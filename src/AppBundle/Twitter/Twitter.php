<?php

namespace AppBundle\Twitter;

use AppBundle\Entity\TwitterSchedule;
use AppBundle\PermalinkManager\SqibePermalinkManager;
use AppBundle\Pollution\PollutionDataFactory\PollutionDataFactory;
use AppBundle\Twitter\MessageFactory\MessageFactoryInterface;
use Caldera\GeoBasic\Coord\Coord;
use Caldera\GeoBasic\Coord\CoordInterface;
use Codebird\Codebird;
use Cron\CronExpression;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class Twitter
{
    /** @var Doctrine $doctrine */
    protected $doctrine;

    /** @var PollutionDataFactory $pollutionDataFactory */
    protected $pollutionDataFactory;

    /** @var MessageFactoryInterface $messageFactory */
    protected $messageFactory;

    /** @var SqibePermalinkManager $permalinkManager */
    protected $permalinkManager;

    /** @var string $twitterClientId */
    protected $twitterClientId;

    /** @var string $twitterClientSecret */
    protected $twitterClientSecret;

    /** @var array $validScheduleList */
    protected $validScheduleList = [];

    public function __construct(Doctrine $doctrine, PollutionDataFactory $pollutionDataFactory, MessageFactoryInterface $messageFactory, SqibePermalinkManager $permalinkManager, string $twitterClientId, string $twitterClientSecret)
    {
        $this->doctrine = $doctrine;
        $this->pollutionDataFactory = $pollutionDataFactory;
        $this->messageFactory = $messageFactory;
        $this->permalinkManager = $permalinkManager;

        $this->twitterClientId = $twitterClientId;
        $this->twitterClientSecret = $twitterClientSecret;
    }

    public function tweet()
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
                $coord = $this->getCoord($twitterSchedule);

                $boxList = $this->pollutionDataFactory->setCoord($coord)->createDecoratedBoxList();

                $message = $this->createMessage($twitterSchedule, $boxList);

                $twitterToken = $twitterSchedule->getCity()->getTwitterToken();
                $twitterSecret = $twitterSchedule->getCity()->getTwitterSecret();

                $cb->setToken($twitterToken, $twitterSecret);

                $params = [
                    'status' => $message,
                    'lat' => $coord->getLatitude(),
                    'long' => $coord->getLongitude(),
                ];

                $reply = $cb->statuses_update($params);

                $this->validScheduleList[] = $twitterSchedule;
            }
        }
    }

    protected function getCoord(TwitterSchedule $twitterSchedule): CoordInterface
    {
        if ($twitterSchedule->getStation()) {
            return $twitterSchedule->getStation();
        } else {
            $coord = new Coord($twitterSchedule->getLatitude(), $twitterSchedule->getLongitude());

            return $coord;
        }
    }

    protected function getCodeBird(): Codebird
    {
        Codebird::setConsumerKey($this->twitterClientId, $this->twitterClientSecret);

        return Codebird::getInstance();
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

    public function getValidScheduleList(): array
    {
        return $this->validScheduleList;
    }
}
