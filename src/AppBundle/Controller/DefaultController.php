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

        $boxList = $this->getBoxListFromDataList($dataList);

        return $this->render(
            'AppBundle:Default:index.html.twig',
            [
                'boxList' => $boxList
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

    protected function createBox(Data $data): Box
    {
        $box = new Box();
        $pollutant = $this->getPollutantById($data->getPollutant());

        $box
            ->setData($data)
            ->setStation($data->getStation())
            ->setPollutant($pollutant)
        ;

        return $box;
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

    private function getBoxListFromDataList(array $dataList): array
    {
        $boxList = [];

        foreach ($dataList as $data) {
            if ($data) {
                $boxList[] = new Box($data);
            }
        }

        return $boxList;
    }
}
