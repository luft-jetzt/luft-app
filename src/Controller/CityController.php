<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use App\SeoPage\SeoPage;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class CityController extends AbstractController
{
    #[Route('/{citySlug}', name: 'show_city', requirements: ['citySlug' => '^([A-Za-z-]+)$'], options: ['expose' => true])]
    public function showAction(#[MapEntity(expr: 'repository.findOneBySlug(citySlug)')] City $city, SeoPage $seoPage, PollutionDataFactory $pollutionDataFactory, Breadcrumbs $breadcrumbs, RouterInterface $router): Response
    {
        $seoPage
            ->setTitle(sprintf('Luftmesswerte aus %s: Stickstoffdioxid, Feinstaub und Ozon', $city->getName()))
            ->setDescription(sprintf('Aktuelle Schadstoffwerte aus Luftmessstationen in %s: Stickstoffdioxid, Feinstaub und Ozon', $city->getName()));

        $breadcrumbs
            ->addItem('Luft', $router->generate('display'))
            ->addItem($city->getName(), $router->generate('show_city', ['citySlug' => $city->getSlug()]));

        $stationList = $this->getStationListForCity($city);
        $stationViewModelList = $this->createViewModelListForStationList($pollutionDataFactory, $stationList);

        return $this->render('City/show.html.twig', [
            'city' => $city,
            'stationList' => $stationList,
            'stationBoxList' => $stationViewModelList,
        ]);
    }
}
