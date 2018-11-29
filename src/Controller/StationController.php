<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Station;
use App\Pollution\PollutionDataFactory\HistoryDataFactory;
use App\Pollution\PollutionDataFactory\HistoryDataFactoryInterface;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use App\SeoPage\SeoPage;
use App\Util\DateTimeUtil;
use Elastica\Aggregation\DateHistogram;
use FOS\ElasticaBundle\Finder\FinderInterface;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
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
            $seoPage->setTitle(sprintf('Luftmesswerte für die Station %s in %s', $station->getStationCode(), $station->getCity()->getName()));
        } else {
            $seoPage->setTitle(sprintf('Luftmesswerte für die Station %s', $station->getStationCode()));
        }

        return $this->render('Default/station.html.twig', [
            'station' => $station,
            'pollutantList' => $boxList,
        ]);
    }

    public function limitsAction(): Response
    {
        /** @var PaginatedFinderInterface $finder */
        $finder = $this->get('fos_elastica.finder.air_data.data');

        $stationQuery = new \Elastica\Query\Term(['station' => 157]);
        $pollutantQuery = new \Elastica\Query\Term(['pollutant' => 1]);

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            ->addMust($pollutantQuery)
            ->addMust($stationQuery);

        $fromDateTime = new \DateTime('2018-10-01');
        $untilDateTime = new \DateTime('2018-10-10');

        $dateTimeQuery = new \Elastica\Query\Range('dateTime', ['gt' => $fromDateTime->format('Y-m-d H:i:s'), 'lte' => $untilDateTime->format('Y-m-d H:i:s'), 'format' => 'yyyy-MM-dd HH:mm:ss']);

        $boolQuery->addMust($dateTimeQuery);

        $histogram = new \Elastica\Aggregation\DateHistogram('value', 'dateTime', '1D');
        $histogram->setTimezone('Europe/Berlin');
        $histogram->setFormat('yyyy-MM-dd');

        $max = new \Elastica\Aggregation\Max('maxValue');
        $max->setField('value');

        $histogram->addAggregation($max);

        $query = new \Elastica\Query($boolQuery);
        $query->addAggregation($histogram);

        $results = $finder->findPaginated($query);

        $aggregations = $results->getAdapter()->getAggregations();

        return $this->render('Station/limits.html.twig', [
            'results' => $aggregations,
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
}
