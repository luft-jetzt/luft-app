<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Data;
use AppBundle\Entity\Station;
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

    public function getDataListFromStationList(array $stationList): array
    {
        $dataList = [];

        foreach ($stationList as $station) {
            /** @var Data $data */
            $data = $this->getDoctrine()->getRepository('AppBundle:Data')->findOneByStation($station);

            if (!array_key_exists($data->getPollutant(), $dataList)) {
                $dataList[$data->getPollutant()] = $data;
            }
        }

        return $dataList;
    }
}
