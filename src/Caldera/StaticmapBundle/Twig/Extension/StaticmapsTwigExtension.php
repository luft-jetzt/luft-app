<?php

namespace Caldera\StaticmapBundle\Twig\Extension;

use Caldera\GeoBasic\Coord\CoordInterface;

class StaticmapsTwigExtension extends \Twig_Extension
{
    /** @var string $staticmapsHost */
    protected $staticmapsHost = '';

    /** @var array $defaultParameters */
    protected $defaultParameters = [
        'maptype' => 'wikimedia-intl',
        'zoom' => 14,
        'size' => '865x512',
    ];

    public function __construct(string $staticmapsHost)
    {
        $this->staticmapsHost = $staticmapsHost;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('staticmaps', [$this, 'staticmaps',], ['is_safe' => ['raw']]),
        ];
    }

    public function staticmaps(CoordInterface $object, int $width = null, int $height = null, int $zoom = null): string
    {
        $parameters = [
            'center' => sprintf('%f,%f', $object->getLatitude(), $object->getLongitude()),
            'markers' => sprintf('%f,%f,%s,%s,%s', $object->getLatitude(), $object->getLongitude(), 'circle', 'blue', 'flag'),

        ];

        return $this->generateMapUrl($parameters, $width, $height, $zoom);
    }

    public function getName(): string
    {
        return 'staticmaps_extension';
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

        return sprintf('%s/staticmap.php?%s', $this->staticmapsHost, $this->generateMapUrlParameters($parameters));
    }

    protected function generateMapUrlParameters(array $parameters = []): string
    {
        $list = [];

        foreach ($parameters as $key => $value) {
            $list [] = sprintf('%s=%s', $key, $value);
        }

        return implode('&',$list);
    }
}

