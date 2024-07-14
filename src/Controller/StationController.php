<?php declare(strict_types=1);

namespace App\Controller;

use App\Air\Analysis\LimitAnalysis\LimitAnalysisInterface;
use App\Air\PollutionDataFactory\HistoryDataFactoryInterface;
use App\Air\PollutionDataFactory\PollutionDataFactory;
use App\Air\SeoPage\SeoPageInterface;
use App\Entity\Station;
use Carbon\Carbon;
use Flagception\Bundle\FlagceptionBundle\Annotations\Feature;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class StationController extends AbstractController
{
    public function stationAction(#[MapEntity(expr: 'repository.findOneByStationCode(stationCode)')] Station $station, SeoPageInterface $seoPage, PollutionDataFactory $pollutionDataFactory, Breadcrumbs $breadcrumbs, RouterInterface $router): Response
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

    public function limitsAction(#[MapEntity(expr: 'repository.findOneByStationCode(stationCode)')] Station $station, LimitAnalysisInterface $limitAnalysis): Response
    {
        if (!$station) {
            throw $this->createNotFoundException();
        }

        $limitAnalysis
            ->setStation($station)
            ->setFromDateTime(Carbon::now()->startOfMonth())
            ->setUntilDateTime(Carbon::now()->endOfMonth());

        $exceedance = $limitAnalysis->analyze();

        var_dump($exceedance);
        return $this->render('Station/limits.html.twig', [
            'exceedanceJson' => json_encode($exceedance, JSON_THROW_ON_ERROR),
        ]);
    }

    /**
     * @Feature("station_history")
     */
    public function historyAction(#[MapEntity(expr: 'repository.findOneByStationCode(stationCode)')] Station $station, Request $request, SeoPageInterface $seoPage, HistoryDataFactoryInterface $historyDataFactory): Response
    {
        if ($untilDateTimeParam = $request->query->get('until')) {
            try {
                $untilDateTime = Carbon::parse($untilDateTimeParam)->endOfDay();
            } catch (\Exception) {
                $untilDateTime = Carbon::now()->endOfHour();
            }
        } else {
            $untilDateTime = Carbon::now()->endOfHour();
        }

        if ($fromDateTimeParam = $request->query->get('from')) {
            try {
                $fromDateTime = Carbon::parse($fromDateTimeParam)->startOfDay();
            } catch (\Exception) {
                $fromDateTime = Carbon::now()->startOfHour();
                $fromDateTime->sub(new \DateInterval('P3D'));
            }
        } else {
            $fromDateTime = Carbon::now()->startOfHour();
            $fromDateTime->sub(new \DateInterval('P3D'));
        }

        if ($station->getCity()) {
            $seoPage->setTitle(sprintf('Frühere Luftmesswerte für die Station %s — Feinstaub, Stickstoffdioxid und Ozon in %s', $station->getStationCode(), $station->getCity()->getName()));
        } else {
            $seoPage->setTitle(sprintf('Frühere Luftmesswerte für die Station %s', $station->getStationCode()));
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
            $pollutantIdList = [...$pollutantIdList, ...array_keys($dataList)];
        }

        return array_unique($pollutantIdList);
    }
}
