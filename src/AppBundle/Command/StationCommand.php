<?php

namespace AppBundle\Command;

use AppBundle\Entity\Data;
use AppBundle\Entity\Station;
use AppBundle\SourceFetcher\Parser\UbParser;
use AppBundle\SourceFetcher\Persister\Persister;
use AppBundle\SourceFetcher\Query\AbstractQuery;
use AppBundle\SourceFetcher\Query\UbCOQuery;
use AppBundle\SourceFetcher\Query\UbNO2Query;
use AppBundle\SourceFetcher\Query\UbO3Query;
use AppBundle\SourceFetcher\Query\UbPM10Query;
use AppBundle\SourceFetcher\Query\UbSO2Query;
use AppBundle\SourceFetcher\SourceFetcher;
use Curl\Curl;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('luft:station')
            ->setDescription('');
    }


}
