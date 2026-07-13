<?php declare(strict_types=1);

namespace App\Controller;

use App\Air\PollutionDataFactory\PollutionDataFactory;
use App\Air\SeoPage\SeoPageInterface;
use App\Entity\Station;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class StationController extends AbstractController
{
    public function stationAction(#[MapEntity(expr: 'repository.findOneByStationCode(stationCode)')] Station $station, SeoPageInterface $seoPage, PollutionDataFactory $pollutionDataFactory, Breadcrumbs $breadcrumbs, RouterInterface $router): Response
    {
        $viewModelList = $pollutionDataFactory
            ->setStation($station)
            ->createDecoratedPollutantList();

        if ($station->getCity()) {
            $seoPage->setTitle(sprintf('Luftmesswerte für die Station %s — Feinstaub, Stickstoffdioxid und Ozon in %s', $station->getStationCode(), $station->getCity()->getName()));

            $breadcrumbs
                ->addItem('Luft', $router->generate('display'))
                ->addItem($station->getCity()->getName(), $router->generate('show_city', ['citySlug' => $station->getCity()->getSlug()]))
                ->addItem($station->getStationCode(), $router->generate('station', ['stationCode' => $station->getStationCode()]));
        } else {
            $seoPage->setTitle(sprintf('Luftmesswerte für die Station %s', $station->getStationCode()));
        }

        return $this->render('Default/station.html.twig', [
            'station' => $station,
            'pollutantList' => $viewModelList,
        ]);
    }
}
