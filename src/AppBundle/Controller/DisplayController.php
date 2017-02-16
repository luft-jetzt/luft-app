<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Data;
use AppBundle\Entity\Station;
use AppBundle\Pollution\Box\Box;
use AppBundle\Pollution\Pollutant\CO;
use AppBundle\Pollution\Pollutant\NO2;
use AppBundle\Pollution\Pollutant\O3;
use AppBundle\Pollution\Pollutant\PM10;
use AppBundle\Pollution\Pollutant\PollutantInterface;
use AppBundle\Pollution\Pollutant\SO2;
use AppBundle\Repository\DataRepository;
use Caldera\GeoBasic\Coord\Coord;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DisplayController extends Controller
{
    /**
     * @Route("/{stationCode}", name="station", options = { "expose" = true })
     */
    public function stationAction(Request $request, string $stationCode)
    {
        $station = $this->getDoctrine()->getRepository('AppBundle:Station')->findOneByStationCode($stationCode);

        $dataList = $this->getDataListFromStationList([$station]);

        $boxList = $this->getBoxListFromDataList($dataList);

        $boxList = $this->decorateBoxList($boxList);

        return $this->render(
            'AppBundle:Default:station.html.twig',
            [
                'station' => $station,
                'boxList' => $boxList
            ]
        );
    }

    /**
     * @Route("/", name="display", options = { "expose" = true })
     */
    public function indexAction(Request $request): Response
    {
        $coord = $this->getCoordByRequest($request);

        if (!$coord) {
            return $this->render('AppBundle:Default:select.html.twig');
        }

        $stationList = $this->findNearestStations($coord);

        if (0 === count($stationList)) {
            return $this->noStationAction($request, $coord);
        }

        $dataList = $this->getDataListFromStationList($stationList);

        $boxList = $this->getBoxListFromDataList($dataList);

        $boxList = $this->decorateBoxList($boxList);

        return $this->render(
            'AppBundle:Default:display.html.twig',
            [
                'boxList' => $boxList
            ]
        );
    }

    public function noStationAction(Request $request, Coord $coord = null): Response
    {
        if (!$coord) {
            $coord = $this->getCoordByRequest($request);
        }

        $stationList = $this->findNearestStations($coord, 1000);

        return $this->render(
            'AppBundle:Default:nostations.html.twig',
            [
                'stationList' => $stationList
            ]);
    }

    protected function getCoordByRequest(Request $request): ?Coord
    {
        $latitude = $request->query->get('latitude');
        $longitude = $request->query->get('longitude');
        $zipCode = $request->query->get('zip');

        if (!$latitude && !$longitude && $zipCode) {
            $zip = $this->getDoctrine()->getRepository('AppBundle:Zip')->findOneByZip($zipCode);

            return $zip;
        }

        if ($latitude && $longitude && !$zipCode) {
            $coord = new Coord(
                $latitude,
                $longitude
            );

            return $coord;
        }

        return null;
    }

    protected function findNearestStations(Coord $coord, int $distance = 20): array
    {
        $finder = $this->container->get('fos_elastica.finder.air.station');

        $geoFilter = new \Elastica\Filter\GeoDistance(
            'pin',
            [
                'lat' => $coord->getLatitude(),
                'lon' => $coord->getLongitude()
            ],
            $distance.'km'
        );

        $filteredQuery = new \Elastica\Query\Filtered(new \Elastica\Query\MatchAll(), $geoFilter);

        $query = new \Elastica\Query($filteredQuery);

        $query->setSize(15);
        $query->setSort(
            [
                '_geo_distance' =>
                    [
                        'pin' =>
                            [
                                'lat' => $coord->getLatitude(),
                                'lon' => $coord->getLongitude()
                            ],
                        'order' => 'asc',
                        'unit' => 'km'
                    ]
            ]
        );

        $results = $finder->find($query);

        return $results;
    }

    protected function getDataListFromStationList(array $stationList): array
    {
        $dataList = [
            PollutantInterface::POLLUTANT_PM10 => null,
            PollutantInterface::POLLUTANT_O3 => null,
            PollutantInterface::POLLUTANT_NO2 => null,
            PollutantInterface::POLLUTANT_SO2 => null,
            PollutantInterface::POLLUTANT_CO => null,
        ];

        foreach ($stationList as $station) {
            foreach ($dataList as $pollutant => $data) {
                if (!$data) {
                    $data = $this->checkStationData($station, $pollutant);

                    if ($data) {
                        $dataList[$pollutant] = $data;
                    }
                }
            }
        }

        return $dataList;
    }

    protected function checkStationData(Station $station, string $pollutant): ?Data
    {
        /** @var DataRepository $repository */
        $repository = $this->getDoctrine()->getRepository('AppBundle:Data');

        return $repository->findLatestDataForStationAndPollutant($station, $pollutant);
    }

    protected function getPollutantById(int $pollutantId): PollutantInterface
    {
        switch ($pollutantId) {
            case 1: return new PM10();
            case 2: return new O3();
            case 3: return new NO2();
            case 4: return new SO2();
            case 5: return new CO();
        }
    }

    protected function getBoxListFromDataList(array $dataList): array
    {
        $boxList = [];

        foreach ($dataList as $data) {
            if ($data) {
                $boxList[] = new Box($data);
            }
        }

        return $boxList;
    }

    protected function decorateBoxList(array $boxList): array
    {
        /** @var Box $box */
        foreach ($boxList as $box) {
            $data = $box->getData();

            $pollutant = $this->getPollutantById($data->getPollutant());
            $level = $pollutant->getPollutionLevel()->getLevel($data);

            $box
                ->setStation($data->getStation())
                ->setPollutant($pollutant)
                ->setPollutionLevel($level)
            ;
        }
        return $boxList;
    }

}
