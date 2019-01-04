<?php declare(strict_types=1);

namespace App\Controller;

use App\Analysis\LimitAnalysis\LimitAnalysisInterface;
use App\Entity\Station;
use App\Plotter\StationPlotter\StationPlotterInterface;
use App\Pollution\PollutionDataFactory\HistoryDataFactoryInterface;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use App\SeoPage\SeoPage;
use App\SeoPage\SeoPageInterface;
use App\Util\DateTimeUtil;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

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

        $limitAnalysis
            ->setStation($station)
            ->setFromDateTime(new \DateTime('2018-11-01'))
            ->setUntilDateTime(new \DateTime('2018-11-30'));

        $resultList = $limitAnalysis->analyze();

        return $this->render('Station/limits.html.twig', [
            'resultList' => $resultList,
            'time' => new \DateTime('2018-11-01'),
        ]);
    }

    public function historyAction(Request $request, string $stationCode, HistoryDataFactoryInterface $historyDataFactory, SeoPageInterface $seoPage, RouterInterface $router): Response
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

        $seoPage->setOpenGraphPreviewPhoto($router->generate('station_history_plot', [
            'stationCode' => $station->getStationCode(),
            'from' => $fromDateTime->format('Y-m-d'),
            'until' => $untilDateTime->format('Y-m-d'),
            'width' => 1200,
            'height' => 630,
        ]));

        $seoPage->setTwitterPreviewPhoto($router->generate('station_history_plot', [
            'stationCode' => $station->getStationCode(),
            'from' => $fromDateTime->format('Y-m-d'),
            'until' => $untilDateTime->format('Y-m-d'),
            'width' => 900,
            'height' => 450,
        ]));

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

    public function plotHistoryAction(Request $request, string $stationCode, StationPlotterInterface $stationPlotter, string $graphCacheDirectory): BinaryFileResponse
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

        $width = (int) $request->get('width', 800);
        $height = (int) $request->get('height', 400);

        $filename = sprintf('%s/%s-%d-%d-%dx%d.png', $graphCacheDirectory, $station->getStationCode(), $fromDateTime->format('U'), $untilDateTime->format('U'), $width, $height);

        if (!file_exists($filename)) {
            $stationPlotter
                ->setWidth($width)
                ->setHeight($height)
                ->setTitle(sprintf('Messwerte der Station %s', $station->getStationCode()))
                ->setStation($station)
                ->setFromDateTime($fromDateTime)
                ->setUntilDateTime($untilDateTime)
                ->plot($filename);
        }

        $response = new BinaryFileResponse($filename);

        return $response;
    }
}
