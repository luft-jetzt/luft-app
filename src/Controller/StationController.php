<?php declare(strict_types=1);

namespace App\Controller;

use App\Analysis\LimitAnalysis\LimitAnalysisInterface;
use App\Entity\City;
use App\Entity\Station;
use App\Plotter\StationPlotter\StationPlotterInterface;
use App\Pollution\PollutionDataFactory\HistoryDataFactoryInterface;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use App\SeoPage\SeoPageInterface;
use App\Util\DateTimeUtil;
use Flagception\Bundle\FlagceptionBundle\Annotations\Feature;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class StationController extends AbstractController
{
    /**
     * @Entity("station", expr="repository.findOneByStationCode(stationCode)")
     */
    public function stationAction(SeoPageInterface $seoPage, Station $station, PollutionDataFactory $pollutionDataFactory, Breadcrumbs $breadcrumbs, RouterInterface $router): Response
    {
        $viewModelList = $pollutionDataFactory
            ->setStation($station)
            ->createDecoratedPollutantList();

        if ($station->getCity()) {
            $seoPage->setTitle(sprintf('Luftmesswerte für die Station %s — Feinstaub, Stickstoffdioxid und Ozon in %s', $station->getStationCode(), $station->getCity()->getName()));

            $breadcrumbs
                ->addItem('Luft', $router->generate('display'))
                ->addItem($station->getCity()->getName(), $router->generate('show_city', ['citySlug' => $station->getCity()->getSlug()]))
                ->addItem($station->getStationCode(), $router->generate('station', ['stationCode' => $station->getStationCode()]));
        } else {
            $seoPage->setTitle(sprintf('Luftmesswerte für die Station %s', $station->getStationCode()));
        }

        return $this->render('Default/station.html.twig', [
            'station' => $station,
            'pollutantList' => $viewModelList,
        ]);
    }

    /**
     * @Entity("station", expr="repository.findOneByStationCode(stationCode)")
     */
    public function limitsAction(LimitAnalysisInterface $limitAnalysis, Station $station): Response
    {
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

    /**
     * @Feature("station_history")
     * @Entity("station", expr="repository.findOneByStationCode(stationCode)")
     */
    public function historyAction(Request $request, Station $station, SeoPageInterface $seoPage, HistoryDataFactoryInterface $historyDataFactory, RouterInterface $router): Response
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

        if ($station->getCity()) {
            $seoPage->setTitle(sprintf('Frühere Luftmesswerte für die Station %s — Feinstaub, Stickstoffdioxid und Ozon in %s', $station->getStationCode(), $station->getCity()->getName()));
        } else {
            $seoPage->setTitle(sprintf('Frühere Luftmesswerte für die Station %s', $station->getStationCode()));
        }

        $seoPage->setOpenGraphPreviewPhoto($router->generate('station_history_plot', [
            'stationCode' => $station->getStationCode(),
            'from' => $fromDateTime->format('Y-m-d'),
            'until' => $untilDateTime->format('Y-m-d'),
            'width' => 1200,
            'height' => 630,
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

    /**
     * @Feature("station_history")
     * @Entity("station", expr="repository.findOneByStationCode(stationCode)")
     */
    public function plotHistoryAction(Request $request, Station $station, StationPlotterInterface $stationPlotter, string $graphCacheDirectory): BinaryFileResponse
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
