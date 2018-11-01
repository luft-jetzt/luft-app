<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\TwitterSchedule;
use App\Twitter\TwitterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TweetCommand extends Command
{
    protected $twitter;

    public function __construct(?string $name = null, TwitterInterface $twitter)
    {
        $this->twitter = $twitter;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('luft:tweet')
            ->setDescription('Post current data')
            ->addOption('dry-run');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('dry-run')) {
            $this->twitter->setDryRun(true);
        }

        $this->twitter->tweet();

        $validScheduleList = $this->twitter->getValidScheduleList();

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
