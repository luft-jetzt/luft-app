<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Station;
use App\Pollution\PollutionDataFactory\HistoryDataFactory;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use App\SeoPage\SeoPage;
use Symfony\Component\HttpFoundation\Response;

class StationController extends AbstractController
{
    public function stationAction(SeoPage $seoPage, string $stationCode, PollutionDataFactory $pollutionDataFactory): Response
    {
        /** @var Station $station */
        $station = $this->getDoctrine()->getRepository(Station::class)->findOneByStationCode($stationCode);

        if (!$station) {
            throw $this->createNotFoundException();
        }

        $boxList = $pollutionDataFactory
            ->setStation($station)
            ->createDecoratedPollutantList();

        if ($station->getCity()) {
            $seoPage->setTitle(sprintf('Luftmesswerte für die Station %s in %s', $station->getStationCode(), $station->getCity()->getName()));
        } else {
            $seoPage->setTitle(sprintf('Luftmesswerte für die Station %s', $station->getStationCode()));
        }

        return $this->render('Default/station.html.twig', [
            'station' => $station,
            'pollutantList' => $boxList,
        ]);
    }

    public function historyAction(string $stationCode, HistoryDataFactory $historyDataFactory): Response
    {
        /** @var Station $station */
        $station = $this->getDoctrine()->getRepository(Station::class)->findOneByStationCode($stationCode);

        if (!$station) {
            throw $this->createNotFoundException();
        }

        $dataLists = $historyDataFactory
            ->setStation($station)
            ->getDataListsForInterval(new \DateTime('2018-11-01'), new \DateTime());

        return $this->render('Station/history.html.twig', [
            'station' => $station,
            'dataLists' => $dataLists,
        ]);
    }
}
