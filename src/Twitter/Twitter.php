<?php declare(strict_types=1);

namespace App\Twitter;

use App\Entity\TwitterSchedule;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use App\Twitter\MessageFactory\MessageFactoryInterface;
use App\YourlsApiManager\LuftYourlsApiManager;
use Caldera\GeoBasic\Coord\Coord;
use Caldera\GeoBasic\Coord\CoordInterface;
use Codebird\Codebird;
use Cron\CronExpression;
use Symfony\Bridge\Doctrine\RegistryInterface as Doctrine;
use Psr\Log\LoggerInterface;

class Twitter
{
    /** @var Doctrine $doctrine */
    protected $doctrine;

    /** @var PollutionDataFactory $pollutionDataFactory */
    protected $pollutionDataFactory;

    /** @var MessageFactoryInterface $messageFactory */
    protected $messageFactory;

    /** @var LuftYourlsApiManager $permalinkManager */
    protected $permalinkManager;

    /** @var LoggerInterface $logger */
    protected $logger;

    /** @var string $twitterClientId */
    protected $twitterClientId;

    /** @var string $twitterClientSecret */
    protected $twitterClientSecret;

    /** @var array $validScheduleList */
    protected $validScheduleList = [];

    public function __construct(Doctrine $doctrine, PollutionDataFactory $pollutionDataFactory, MessageFactoryInterface $messageFactory, LuftYourlsApiManager $permalinkManager, LoggerInterface $logger, string $twitterClientId, string $twitterClientSecret)
    {
        $this->doctrine = $doctrine;
        $this->pollutionDataFactory = $pollutionDataFactory;
        $this->messageFactory = $messageFactory;
        $this->permalinkManager = $permalinkManager;
        $this->logger = $logger;

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
