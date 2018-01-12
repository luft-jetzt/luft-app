<?php declare(strict_types=1);

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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TweetCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('luft:tweet')
            ->setDescription('Post current data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $twitter = $this->getContainer()->get(Twitter::class);

        $twitter->tweet();

        $validScheduleList = $twitter->getValidScheduleList();

        $table = new Table($output);
        $table->setHeaders(['City', 'Title', 'Cron', 'Twitter', 'Station', 'Station Code', 'Latitude', 'Longitude']);

        /** @var TwitterSchedule $validSchedule */
        foreach ($validScheduleList as $validSchedule) {
            $table->addRow([
                $validSchedule->getCity()->getName(),
                $validSchedule->getTitle(),
                $validSchedule->getCron(),
                sprintf('@%s', $validSchedule->getCity()->getUser()->getUsername()),
                $validSchedule->getStation() ? $validSchedule->getStation()->getTitle() : '',
                $validSchedule->getStation() ? $validSchedule->getStation()->getStationCode() : '',
                $validSchedule->getLatitude(),
                $validSchedule->getLongitude(),
            ]);
        }

        $table->render();
    }
}
