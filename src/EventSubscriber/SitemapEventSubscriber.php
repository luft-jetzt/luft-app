<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\City;
use App\Entity\Station;
use App\Pollution\Pollutant\PollutantInterface;
use App\Pollution\PollutantList\PollutantListInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\Routing\RouterInterface;

class SitemapEventSubscriber implements EventSubscriberInterface
{
    /** @var UrlGeneratorInterface $urlGenerator */
    protected $urlGenerator;

    /** @var RouterInterface $router */
    protected $router;

    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var PollutantListInterface $pollutantList */
    protected $pollutantList;

    public function __construct(UrlGeneratorInterface $urlGenerator, RouterInterface $router, RegistryInterface $registry, PollutantListInterface $pollutantList)
    {
        $this->urlGenerator = $urlGenerator;
        $this->router = $router;
        $this->registry = $registry;
        $this->pollutantList = $pollutantList;
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
        /** @var PollutantInterface $pollutant */
        foreach ($this->pollutantList->getPollutants() as $pollutant) {
            $routeName = sprintf('pollutant_%s', $pollutant->getIdentifier());

            if ($this->router->getRouteCollection()->get($routeName)) {
                $url = $this->urlGenerator->generate($routeName, [], UrlGeneratorInterface::ABSOLUTE_URL);

                $urlContainer->addUrl(new UrlConcrete($url), 'pollutant');
            }
        }
    }
}
