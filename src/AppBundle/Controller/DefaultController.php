<?php

namespace AppBundle\Controller;

use AppBundle\Pollution\Pollutant\Pollutant;
use AppBundle\Repository\DataRepository;
use Caldera\GeoBasic\Coord\Coord;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $latitude = $request->query->get('latitude');
        $longitude = $request->query->get('longitude');

        if (!$latitude || !$longitude) {
            throw $this->createNotFoundException();
        }

        $coord = new Coord(
            $latitude,
            $longitude
        );

        $stationList = $this->findNearestStations($coord);

        $dataList = $this->getDataListFromStationList($stationList);

        foreach ($dataList as $data) {
            echo $data->getValue();
            echo $data->getStation()->getTitle();
        }
        return $this->render(
            'AppBundle:Default:index.html.twig',
            [
                'dataList' => $dataList
            ]
        );
    }

    protected function findNearestStations(Coord $coord): array
    {
        $finder = $this->container->get('fos_elastica.finder.air.station');

        $geoFilter = new \Elastica\Filter\GeoDistance(
            'pin',
            [
                'lat' => $coord->getLatitude(),
                'lon' => $coord->getLongitude()
            ],
            '10km'
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
                                $coord->getLatitude(),
                                $coord->getLongitude()
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
        /** @var DataRepository $repository */
        $repository = $this->getDoctrine()->getRepository('AppBundle:Data');

        $dataList = [];

        foreach ($stationList as $station) {
            if (!array_key_exists(Pollutant::POLLUTANT_PM10, $dataList)) {
                $data = $repository->findLatestDataForStationAndPollutant($station, Pollutant::POLLUTANT_PM10);

                if ($data) {
                    $dataList[Pollutant::POLLUTANT_PM10] = $data;
                }
            }

            if (!array_key_exists(Pollutant::POLLUTANT_NO2, $dataList)) {
                $data = $repository->findLatestDataForStationAndPollutant($station, Pollutant::POLLUTANT_NO2);

                if ($data) {
                    $dataList[Pollutant::POLLUTANT_NO2] = $data;
                }
            }

            if (!array_key_exists(Pollutant::POLLUTANT_O3, $dataList)) {
                $data = $repository->findLatestDataForStationAndPollutant($station, Pollutant::POLLUTANT_O3);

                if ($data) {
                    $dataList[Pollutant::POLLUTANT_O3] = $data;
                }
            }

            if (!array_key_exists(Pollutant::POLLUTANT_SO2, $dataList)) {
                $data = $repository->findLatestDataForStationAndPollutant($station, Pollutant::POLLUTANT_SO2);

                if ($data) {
                    $dataList[Pollutant::POLLUTANT_SO2] = $data;
                }
            }

            if (!array_key_exists(Pollutant::POLLUTANT_CO, $dataList)) {
                $data = $repository->findLatestDataForStationAndPollutant($station, Pollutant::POLLUTANT_CO);

                if ($data) {
                    $dataList[Pollutant::POLLUTANT_CO] = $data;
                }
            }
        }

        return $dataList;
    }
}
