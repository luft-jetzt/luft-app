<?php declare(strict_types=1);

namespace App\Twitter;

use App\Entity\TwitterSchedule;
use App\Pollution\PollutantFactoryStrategy\SimplePollutantFactoryStrategy;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use App\Twitter\MessageFactory\MessageFactoryInterface;
use Caldera\GeoBasic\Coord\Coord;
use Caldera\GeoBasic\Coord\CoordInterface;
use Codebird\Codebird;
use Symfony\Bridge\Doctrine\RegistryInterface as Doctrine;
use Psr\Log\LoggerInterface;

abstract class AbstractTwitter implements TwitterInterface
{
    protected Doctrine $doctrine;

    protected PollutionDataFactory $pollutionDataFactory;

    protected MessageFactoryInterface $messageFactory;

    protected LoggerInterface $logger;

    protected string $twitterClientId;

    protected string $twitterClientSecret;

    protected array $validScheduleList = [];

    protected \DateTime $dateTime;

    protected bool $dryRun = false;

    public function __construct(Doctrine $doctrine, PollutionDataFactory $pollutionDataFactory, MessageFactoryInterface $messageFactory, LoggerInterface $logger, string $twitterClientId, string $twitterClientSecret)
    {
        $this->doctrine = $doctrine;
        $this->messageFactory = $messageFactory;
        $this->logger = $logger;
        $this->dateTime = new \DateTime();

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

    public function setDateTime(\DateTime $dateTime): TwitterInterface
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
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
