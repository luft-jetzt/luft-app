<?php

namespace AppBundle\Command;

use AppBundle\Entity\TwitterSchedule;
use AppBundle\Pollution\Box\Box;
use AppBundle\Pollution\PollutionDataFactory\PollutionDataFactory;
use AppBundle\Twitter\MessageFactory\MessageFactoryInterface;
use Caldera\GeoBasic\Coord\Coord;
use Caldera\GeoBasic\Coord\CoordInterface;
use Codebird\Codebird;
use Cron\CronExpression;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TweetCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('luft:tweet')
            ->setDescription('Post current data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $twitterSchedules = $this->getContainer()->get('doctrine')->getRepository(TwitterSchedule::class)->findAll();

        $cb = $this->getCodeBird();

        /** @var TwitterSchedule $twitterSchedule */
        foreach ($twitterSchedules as $twitterSchedule) {
            $output->writeln($twitterSchedule->getCron());

            $cron = CronExpression::factory($twitterSchedule->getCron());

            if ($cron->isDue()) {
                $coord = $this->getCoord($twitterSchedule);

                $boxList = $this->getPollutionDataFactory()->setCoord($coord)->createDecoratedBoxList();

                $message = $this->createMessage($twitterSchedule, $boxList);

                $twitterToken = $twitterSchedule->getCity()->getTwitterToken();
                $twitterSecret = $twitterSchedule->getCity()->getTwitterSecret();

                $cb->setToken($twitterToken, $twitterSecret);

                $reply = $cb->statuses_update(sprintf('status=%s', $message));

                var_dump($reply);
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
        $twitterClientId = $this->getContainer()->getParameter('twitter.client_id');
        $twitterClientSecret = $this->getContainer()->getParameter('twitter.client_secret');

        Codebird::setConsumerKey($twitterClientId, $twitterClientSecret);

        return Codebird::getInstance();
    }

    protected function getPollutionDataFactory(): PollutionDataFactory
    {
        return $this->getContainer()->get('AppBundle\Pollution\PollutionDataFactory\PollutionDataFactory');
    }

    protected function createMessage(TwitterSchedule $twitterSchedule, array $boxList): string
    {
        /** @var MessageFactoryInterface $factory */
        $factory = $this->getContainer()->get('AppBundle\Twitter\MessageFactory\EmojiMessageFactory');

        $message = $factory
            ->setTitle($twitterSchedule->getTitle())
            ->setBoxList($boxList)
            ->compose()
            ->getMessage()
        ;

        return urlencode($message);
    }
}
