<?php

namespace AppBundle\PermalinkManager;

use AppBundle\Entity\Photo;
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

    public function createPermalink(Photo $photo): string
    {
        $data = [
            'url' => $this->generateUrl($photo),
            'title' => $photo->getTitle(),
            'format'   => 'json',
            'action'   => 'shorturl'
        ];

        $response = $this->postCurl($data);

        if (!isset($response->shorturl)) {
            return '';
        }

        $permalink = $response->shorturl;
        $photo->setPermalink($permalink);

        return $permalink;
    }

    public function getUrl(Photo $photo): string
    {
        $data = [
            'shorturl' => $this->getKeyword($photo),
            'format'   => 'json',
            'action'   => 'expand'
        ];

        $response = $this->postCurl($data);

        if (isset($response->errorCode) && $response->errorCode == 404) {
            return '';
        }

        $longUrl = $response->longurl;

        return $longUrl;
    }

    public function updatePermalink(Photo $photo): bool
    {
        $url = $this->generateUrl($photo);

        $data = [
            'url' => $url,
            'shorturl' => $this->getKeyword($photo),
            'format'   => 'json',
            'action'   => 'update'
        ];

        $response = $this->postCurl($data);

        if (isset($response->statusCode) && $response->statusCode == 200) {
            return true;
        }

        return false;
    }

    protected function getKeyword(Photo $photo): string
    {
        $permalinkParts = explode('/', $photo->getPermalink());
        $keyword = array_pop($permalinkParts);

        return $keyword;
    }

    protected function generateUrl(Photo $photo): string
    {
        $url = $this->router->generate(
            'show_photo',
            [
                'slug' => $photo->getSlug()
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

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