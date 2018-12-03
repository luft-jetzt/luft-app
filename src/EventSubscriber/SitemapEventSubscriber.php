<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Station;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class SitemapEventSubscriber implements EventSubscriberInterface
{
    /** @var UrlGeneratorInterface $urlGenerator */
    private $urlGenerator;

    /** @var RegistryInterface $registry */
    private $registry;

    public function __construct(UrlGeneratorInterface $urlGenerator, RegistryInterface $registry)
    {
        $this->urlGenerator = $urlGenerator;
        $this->registry = $registry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => 'populate',
        ];
    }

    public function populate(SitemapPopulateEvent $event): void
    {
        $this->registerStationUrls($event->getUrlContainer());
    }

    public function registerStationUrls(UrlContainerInterface $urlContainer): void
    {
        $stationList = $this->registry->getRepository(Station::class)->findActiveStations();

        /** @var Station $station */
        foreach ($stationList as $station) {
            $url = $this->urlGenerator->generate('station', ['stationCode' => $station->getStationCode()], UrlGeneratorInterface::ABSOLUTE_URL);

            $urlContainer->addUrl(new UrlConcrete($url), 'station');
        }
    }
}
