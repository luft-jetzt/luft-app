<?php

namespace AppBundle\Command;

use AppBundle\Entity\TwitterSchedule;
use AppBundle\PermalinkManager\SqibePermalinkManager;
use AppBundle\Pollution\Box\Box;
use AppBundle\Pollution\PollutionDataFactory\PollutionDataFactory;
use AppBundle\Twitter\MessageFactory\EmojiMessageFactory;
use AppBundle\Twitter\MessageFactory\MessageFactoryInterface;
use AppBundle\Twitter\Twitter;
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
        $twitter = $this->getContainer()->get(Twitter::class);

        $twitter->tweet();
    }
}
