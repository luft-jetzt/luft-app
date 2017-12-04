<?php

namespace AppBundle\Command;

use AppBundle\Entity\TwitterSchedule;
use AppBundle\Pollution\Box\Box;
use AppBundle\Pollution\PollutionDataFactory\PollutionDataFactory;
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

                $message = $this->createMessage($boxList);

                $cb->setToken($twitterSchedule->getCity()->getTwitterToken(), $twitterSchedule->getCity()->getTwitterSecret());
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
        $twitterClientSecret = $this->getContainer()->getParameter('twitter.client_id');

        Codebird::setConsumerKey($twitterClientId, $twitterClientSecret);

        return Codebird::getInstance();
    }

    protected function getPollutionDataFactory(): PollutionDataFactory
    {
        return $this->getContainer()->get('AppBundle\Pollution\PollutionDataFactory\PollutionDataFactory');
    }

    protected function createMessage(array $boxList): string
    {
        $message = '';

        /** @var Box $box */
        foreach ($boxList as $box) {
            $message .= sprintf('%s: %.0f %s\n', $box->getPollutant()->getName(), $box->getData()->getValue(), $box->getPollutant()->getUnit());
        }

        return $message;
    }
}
