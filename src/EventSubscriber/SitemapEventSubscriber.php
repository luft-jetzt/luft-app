<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Air\MeasurementList\MeasurementListInterface;
use App\Entity\City;
use App\Entity\Station;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\Routing\RouterInterface;

class SitemapEventSubscriber implements EventSubscriberInterface
{
    protected UrlGeneratorInterface $urlGenerator;
    protected RouterInterface $router;
    protected ManagerRegistry $registry;
    protected MeasurementListInterface $measurementList;

    public function __construct(UrlGeneratorInterface $urlGenerator, RouterInterface $router, ManagerRegistry $registry, MeasurementListInterface $measurementList)
    {
        $this->urlGenerator = $urlGenerator;
        $this->router = $router;
        $this->registry = $registry;
        $this->measurementList = $measurementList;
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
        $this->registerCityUrls($event->getUrlContainer());
        $this->registerPollutantUrls($event->getUrlContainer());
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

    public function registerCityUrls(UrlContainerInterface $urlContainer): void
    {
        $cityList = $this->registry->getRepository(City::class)->findCitiesWithActiveStations();

        /** @var City $city */
        foreach ($cityList as $city) {
            $url = $this->urlGenerator->generate('show_city', ['citySlug' => $city->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);

            $urlContainer->addUrl(new UrlConcrete($url), 'city');
        }
    }

    public function registerPollutantUrls(UrlContainerInterface $urlContainer): void
    {
        /** @var MeasurementListInterface $measurement */
        foreach ($this->measurementList->getMeasurements() as $measurement) {
            $routeName = sprintf('pollutant_%s', $measurement->getIdentifier());

            if ($this->router->getRouteCollection()->get($routeName)) {
                $url = $this->urlGenerator->generate($routeName, [], UrlGeneratorInterface::ABSOLUTE_URL);

                $urlContainer->addUrl(new UrlConcrete($url), 'pollutant');
            }
        }
    }
}
