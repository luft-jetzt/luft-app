<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Air\PollutantList\PollutantListInterface;
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
    public function __construct(protected UrlGeneratorInterface $urlGenerator, protected RouterInterface $router, protected ManagerRegistry $registry, protected PollutantListInterface $pollutantList)
    {
    }

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            SitemapPopulateEvent::class => 'populate',
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
        /** @var PollutantListInterface $pollutant */
        foreach ($this->pollutantList->getPollutants() as $pollutant) {
            $routeName = sprintf('pollutant_%s', $pollutant->getIdentifier());

            if ($this->router->getRouteCollection()->get($routeName)) {
                $url = $this->urlGenerator->generate($routeName, [], UrlGeneratorInterface::ABSOLUTE_URL);

                $urlContainer->addUrl(new UrlConcrete($url), 'pollutant');
            }
        }
    }
}
