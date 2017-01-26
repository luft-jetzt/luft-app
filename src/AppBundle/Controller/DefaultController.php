<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Data;
use AppBundle\Entity\Station;
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
            if (!array_key_exists(Data::POLLUTANT_PM10, $dataList)) {
                $data = $repository->findLatestDataForStationAndPollutant($station, Data::POLLUTANT_PM10);

                if ($data) {
                    $dataList[Data::POLLUTANT_PM10] = $data;
                }
            }

            if (!array_key_exists(Data::POLLUTANT_NO2, $dataList)) {
                $data = $repository->findLatestDataForStationAndPollutant($station, Data::POLLUTANT_NO2);

                if ($data) {
                    $dataList[Data::POLLUTANT_NO2] = $data;
                }
            }

            if (!array_key_exists(Data::POLLUTANT_O3, $dataList)) {
                $data = $repository->findLatestDataForStationAndPollutant($station, Data::POLLUTANT_O3);

                if ($data) {
                    $dataList[Data::POLLUTANT_O3] = $data;
                }
            }

            if (!array_key_exists(Data::POLLUTANT_SO2, $dataList)) {
                $data = $repository->findLatestDataForStationAndPollutant($station, Data::POLLUTANT_SO2);

                if ($data) {
                    $dataList[Data::POLLUTANT_SO2] = $data;
                }
            }

            if (!array_key_exists(Data::POLLUTANT_CO, $dataList)) {
                $data = $repository->findLatestDataForStationAndPollutant($station, Data::POLLUTANT_CO);

                if ($data) {
                    $dataList[Data::POLLUTANT_CO] = $data;
                }
            }
        }

        return $dataList;
    }
}
