<?php declare(strict_types=1);

namespace App\StaticMap\UrlGenerator;

use App\Entity\City;
use App\Entity\Station;
use App\StaticMap\StaticMapableInterface;

class UrlGenerator extends AbstractUrlGenerator
{
    public function generate(StaticMapableInterface $object, int $width = null, int $height = null, int $zoom = null): string
    {
        if ($object instanceof Station) {
            return $this->staticmapsStation($object, $width, $height, $zoom);
        } elseif ($object instanceof City) {
            return $this->staticmapsCity($object, $width, $height, $zoom);
        }

        return '';
    }

    public function staticmapsCity(City $city, int $width = null, int $height = null, int $zoom = null): string
    {
        $parameters = [
            'markers' => sprintf('%f,%f,%s,%s,%s', $city->getLatitude(), $city->getLongitude(), 'circle', 'blue', 'university'),
        ];

        return $this->generateMapUrl($parameters, $width, $height, $zoom);
    }

    public function staticmapsStation(Station $station, int $width = null, int $height = null, int $zoom = null): string
    {
        $parameters = [
            'markers' => sprintf('%f,%f,%s,%s,%s', $station->getLatitude(), $station->getLongitude(), 'circle', 'blue', 'university'),
        ];

        return $this->generateMapUrl($parameters, $width, $height, $zoom);
    }

    protected function generateMapUrl(array $parameters = [], int $width = null, int $height = null, int $zoom = null): string
    {
        $viewParameters = [];

        if ($width && $height) {
            $viewParameters['size'] = sprintf('%dx%d', $width, $height);
        }

        if ($zoom) {
            $viewParameters['zoom'] = sprintf('%d', $zoom);
        }

        $parameters = array_merge($parameters, $this->defaultParameters, $viewParameters);

        return sprintf('%sstaticmap.php?%s', $this->staticmapsHost, $this->generateMapUrlParameters($parameters));
    }

    protected function generateMapUrlParameters(array $parameters = []): string
    {
        $list = [];

        foreach ($parameters as $key => $value) {
            $list [] = sprintf('%s=%s', $key, $value);
        }

        return implode('&', $list);
    }
}
