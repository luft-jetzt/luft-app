<?php declare(strict_types=1);

namespace App\Controller;

use App\Analysis\LimitAnalysis\LimitAnalysisInterface;
use App\Entity\Station;
use App\Plotter\StationPlotter\StationPlotterInterface;
use App\Pollution\PollutionDataFactory\HistoryDataFactoryInterface;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use App\SeoPage\SeoPage;
use App\Util\DateTimeUtil;
use Symfony\Component\HttpFoundation\Request;
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
            $seoPage->setTitle(sprintf('Luftmesswerte für die Station %s — Feinstaub, Stickstoffdioxid und Ozon in %s', $station->getStationCode(), $station->getCity()->getName()));
        } else {
            $seoPage->setTitle(sprintf('Luftmesswerte für die Station %s', $station->getStationCode()));
        }

        return $this->render('Default/station.html.twig', [
            'station' => $station,
            'pollutantList' => $boxList,
        ]);
    }

    public function limitsAction(LimitAnalysisInterface $limitAnalysis, string $stationCode): Response
    {
        /** @var Station $station */
        $station = $this->getDoctrine()->getRepository(Station::class)->findOneByStationCode($stationCode);

        if (!$station) {
            throw $this->createNotFoundException();
        }

        $now = new \DateTime('2018-11-30');

        $limitAnalysis
            ->setStation($station)
            ->setFromDateTime(DateTimeUtil::getMonthStartDateTime($now))
            ->setUntilDateTime(DateTimeUtil::getMonthEndDateTime($now));

        $exceedance = $limitAnalysis->analyze();

        var_dump($exceedance);
        return $this->render('Station/limits.html.twig', [
            'exceedanceJson' => json_encode($exceedance),
        ]);
    }

    public function historyAction(Request $request, string $stationCode, HistoryDataFactoryInterface $historyDataFactory): Response
    {
        if ($untilDateTimeParam = $request->query->get('until')) {
            try {
                $untilDateTime = DateTimeUtil::getDayEndDateTime(new \DateTime($untilDateTimeParam));
            } catch (\Exception $exception) {
                $untilDateTime = DateTimeUtil::getHourStartDateTime(new \DateTime());
            }
        } else {
            $untilDateTime = DateTimeUtil::getHourStartDateTime(new \DateTime());
        }

        if ($fromDateTimeParam = $request->query->get('from')) {
            try {
                $fromDateTime = DateTimeUtil::getDayStartDateTime(new \DateTime($fromDateTimeParam));
            } catch (\Exception $exception) {
                $fromDateTime = DateTimeUtil::getHourStartDateTime(new \DateTime());
                $fromDateTime->sub(new \DateInterval('P3D'));
            }
        } else {
            $fromDateTime = DateTimeUtil::getHourStartDateTime(new \DateTime());
            $fromDateTime->sub(new \DateInterval('P3D'));
        }

        /** @var Station $station */
        $station = $this->getDoctrine()->getRepository(Station::class)->findOneByStationCode($stationCode);

        if (!$station) {
            throw $this->createNotFoundException();
        }

        $dataLists = $historyDataFactory
            ->setStation($station)
            ->createDecoratedPollutantListForInterval($fromDateTime, $untilDateTime);

        krsort($dataLists);

        return $this->render('Station/history.html.twig', [
            'station' => $station,
            'dataLists' => $dataLists,
            'fromDateTime' => $fromDateTime,
            'untilDateTime' => $untilDateTime,
            'pollutantIdList' => $this->findPollutantsFromList($dataLists),
        ]);
    }

    protected function findPollutantsFromList(array $dataLists): array
    {
        $pollutantIdList = [];

        foreach ($dataLists as $dataList) {
            $pollutantIdList = array_merge($pollutantIdList, array_keys($dataList));
        }

        return array_unique($pollutantIdList);
    }

    public function plotHistoryAction(Request $request, string $stationCode, StationPlotterInterface $stationPlotter): void
    {
        if ($untilDateTimeParam = $request->query->get('until')) {
            try {
                $untilDateTime = DateTimeUtil::getDayEndDateTime(new \DateTime($untilDateTimeParam));
            } catch (\Exception $exception) {
                $untilDateTime = DateTimeUtil::getHourStartDateTime(new \DateTime());
            }
        } else {
            $untilDateTime = DateTimeUtil::getHourStartDateTime(new \DateTime());
        }

        if ($fromDateTimeParam = $request->query->get('from')) {
            try {
                $fromDateTime = DateTimeUtil::getDayStartDateTime(new \DateTime($fromDateTimeParam));
            } catch (\Exception $exception) {
                $fromDateTime = DateTimeUtil::getHourStartDateTime(new \DateTime());
                $fromDateTime->sub(new \DateInterval('P3D'));
            }
        } else {
            $fromDateTime = DateTimeUtil::getHourStartDateTime(new \DateTime());
            $fromDateTime->sub(new \DateInterval('P3D'));
        }

        /** @var Station $station */
        $station = $this->getDoctrine()->getRepository(Station::class)->findOneByStationCode($stationCode);

        if (!$station) {
            throw $this->createNotFoundException();
        }

        $stationPlotter
            ->setWidth(800)
            ->setHeight(400)
            ->setTitle(sprintf('Messwerte der Station %s', $station->getStationCode()))
            ->setStation($station)
            ->setFromDateTime($fromDateTime)
            ->setUntilDateTime($untilDateTime)
            ->plot();
    }
}
