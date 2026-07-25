<?php declare(strict_types=1);

namespace App\Controller;

use App\Air\PollutionDataFactory\PollutionDataFactory;
use App\Entity\Station;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Nicht-öffentliche Design-Vorschau für die Neugestaltung „Atmosphäre" (Prototyp 01).
 * Rendert die neuen Screens im echten Asset-Build, ohne die Live-Seiten zu verändern.
 * Bewusst noindex; wird nach Abschluss der Migration entfernt.
 */
final class AtmosphaerePreviewController extends AbstractController
{
    #[Route('/vorschau/atmosphaere', name: 'atmosphaere_preview', methods: ['GET'])]
    public function index(): Response
    {
        return $this->noindex($this->render('Atmosphaere/preview.html.twig'));
    }

    /**
     * Neue Stationsseite mit ECHTEN Daten — zur Verifikation vor der Live-Umstellung.
     */
    #[Route('/vorschau/station/{stationCode}', name: 'atmosphaere_preview_station', methods: ['GET'])]
    public function station(
        #[MapEntity(expr: 'repository.findOneByStationCode(stationCode)')] Station $station,
        PollutionDataFactory $pollutionDataFactory,
        SeoPageInterface $seoPage,
    ): Response {
        $viewModelList = $pollutionDataFactory->setStation($station)->createDecoratedPollutantList();
        $seoPage->setTitle(sprintf('Luftqualität an der Station %s — luft.jetzt', $station->getStationCode()));

        return $this->noindex($this->render('Atmosphaere/station.html.twig', [
            'station' => $station,
            'pollutantList' => $viewModelList,
        ]));
    }

    private function noindex(Response $response): Response
    {
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }
}
