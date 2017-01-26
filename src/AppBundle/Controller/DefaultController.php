<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Data;
use AppBundle\Entity\Station;
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
        $dataList = [
            Pollutant::POLLUTANT_PM10 => null,
            Pollutant::POLLUTANT_O3 => null,
            Pollutant::POLLUTANT_NO2 => null,
            Pollutant::POLLUTANT_SO2 => null,
            Pollutant::POLLUTANT_CO => null,
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
}
