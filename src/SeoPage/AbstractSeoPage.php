<?php declare(strict_types=1);

namespace App\SeoPage;

use Sonata\SeoBundle\Seo\SeoPageInterface as SonataSeoPageInterface;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractSeoPage implements SeoPageInterface
{
    /** @var SonataSeoPageInterface $sonataSeoPage*/
    protected $sonataSeoPage;

    /** @var string $assetsVersion */
    protected $assetsVersion;

    /** @var RequestStack $requestStack */
    protected $requestStack;

    public function __construct(SonataSeoPageInterface $sonataSeoPage, RequestStack $requestStack, string $assetsVersion)
    {
        $this->sonataSeoPage = $sonataSeoPage;
        $this->requestStack = $requestStack;
        $this->assetsVersion = $assetsVersion;
    }

    protected function getRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    protected function asset(string $filename): string
    {
        $host = $this->getRequest()->getSchemeAndHttpHost();

        $package = new Package(new StaticVersionStrategy($this->assetsVersion));

        return $package->getUrl(sprintf('%s%s', $host, '/img/share/opengraph.jpeg'));
    }
}
