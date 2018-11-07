<?php declare(strict_types=1);

namespace App\Twitter;

use App\Entity\TwitterSchedule;
use App\Pollution\PollutantFactoryStrategy\SimplePollutantFactoryStrategy;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use App\Twitter\MessageFactory\MessageFactoryInterface;
use App\YourlsApiManager\LuftYourlsApiManager;
use Caldera\GeoBasic\Coord\Coord;
use Caldera\GeoBasic\Coord\CoordInterface;
use Codebird\Codebird;
use Symfony\Bridge\Doctrine\RegistryInterface as Doctrine;
use Psr\Log\LoggerInterface;

abstract class AbstractTwitter implements TwitterInterface
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

    /** @var bool $dryRun */
    protected $dryRun = false;

    public function __construct(Doctrine $doctrine, PollutionDataFactory $pollutionDataFactory, MessageFactoryInterface $messageFactory, LuftYourlsApiManager $permalinkManager, LoggerInterface $logger, string $twitterClientId, string $twitterClientSecret)
    {
        $this->doctrine = $doctrine;
        $this->messageFactory = $messageFactory;
        $this->permalinkManager = $permalinkManager;
        $this->logger = $logger;

        $this->twitterClientId = $twitterClientId;
        $this->twitterClientSecret = $twitterClientSecret;

        // @todo do this via services please!
        $this->pollutionDataFactory = $pollutionDataFactory;
        $this->pollutionDataFactory->setStrategy(new SimplePollutantFactoryStrategy());
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

    public function getValidScheduleList(): array
    {
        return $this->validScheduleList;
    }

    public function getDryRun(): bool
    {
        return $this->dryRun;
    }

    public function setDryRun(bool $dryRun): TwitterInterface
    {
        $this->dryRun = $dryRun;

        return $this;
    }
}
