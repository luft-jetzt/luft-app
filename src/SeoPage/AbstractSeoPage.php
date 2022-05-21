<?php declare(strict_types=1);

namespace App\SeoPage;

use Sonata\SeoBundle\Seo\SeoPageInterface as SonataSeoPageInterface;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractSeoPage implements SeoPageInterface
{
    protected SonataSeoPageInterface $sonataSeoPage;

    protected RequestStack $requestStack;

    public function __construct(SonataSeoPageInterface $sonataSeoPage, RequestStack $requestStack)
    {
        $this->sonataSeoPage = $sonataSeoPage;
        $this->requestStack = $requestStack;
    }

    protected function getRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    protected function asset(string $filename): string
    {
        $host = $this->getRequest()->getSchemeAndHttpHost();

        $package = new Package(new StaticVersionStrategy((new \DateTime())->format('Y-m-d')));

        return $package->getUrl(sprintf('%s%s', $host, $filename));
    }
}
