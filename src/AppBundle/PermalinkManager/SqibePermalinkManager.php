<?php

namespace AppBundle\PermalinkManager;

use AppBundle\Entity\Photo;
use AppBundle\Entity\Station;
use AppBundle\Entity\TwitterSchedule;
use Caldera\GeoBasic\Coord\Coord;
use Curl\Curl;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SqibePermalinkManager
{
    /** @var Router $router */
    protected $router;

    /** @var string $apiUrl */
    protected $apiUrl;

    /** @var string $apiUsername */
    protected $apiUsername;

    /** @var string $apiPassword */
    protected $apiPassword;

    public function __construct(Router $router, string $apiUrl, string $apiUsername, string $apiPassword)
    {
        $this->router = $router;

        $this->apiUrl = $apiUrl;
        $this->apiUsername = $apiUsername;
        $this->apiPassword = $apiPassword;
    }

    public function createPermalinkForTweet(TwitterSchedule $twitterSchedule): string
    {
        $dateTime = new \DateTime();

        $data = [
            'title' => $twitterSchedule->getTitle(),
            'format'   => 'json',
            'action'   => 'shorturl'
        ];

        if ($twitterSchedule->getStation()) {
            $data['url'] = $this->generateUrlForStation($twitterSchedule->getStation(), $dateTime);
        } else {
            $coord = new Coord($twitterSchedule->getLatitude(), $twitterSchedule->getLongitude());

            $data['url'] = $this->generateUrlForCoord($coord, $dateTime);
        }

        $response = $this->postCurl($data);

        if (!isset($response->shorturl)) {
            return '';
        }

        $permalink = $response->shorturl;
        $photo->setPermalink($permalink);

        return $permalink;
    }


    protected function generateUrlForStation(Station $station, \DateTimeInterface $dateTime): string
    {
        $url = $this->router->generate('station', ['stationCode' => $station->getStationCode(), 'timestamp' => $dateTime->format('U')], UrlGeneratorInterface::ABSOLUTE_URL);

        $url = str_replace('http://', 'https://', $url);

        return $url;
    }

    protected function generateUrlForCoord(Coord $coord, \DateTimeInterface $dateTime): string
    {
        $url = $this->router->generate('station', ['latitude' => $coord->getLatitude(), 'longitude' => $coord->getLongitude(), 'timestamp' => $dateTime->format('U')], UrlGeneratorInterface::ABSOLUTE_URL);

        $url = str_replace('http://', 'https://', $url);

        return $url;
    }

    protected function postCurl(array $data): \stdClass
    {
        $loginArray = [
            'username' => $this->apiUsername,
            'password' => $this->apiPassword
        ];

        $data = array_merge($data, $loginArray);

        $curl = new Curl();
        $curl->post(
            $this->apiUrl,
            $data
        );

        return $curl->response;
    }
}
