<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Nicht-öffentliche Design-Vorschau für die Neugestaltung „Atmosphäre" (Prototyp 01).
 * Rendert den neuen Ergebnis-Screen im echten Asset-Build, ohne die Live-Seiten zu verändern.
 * Bewusst noindex; wird nach Abschluss der Migration entfernt.
 */
final class AtmosphaerePreviewController extends AbstractController
{
    #[Route('/vorschau/atmosphaere', name: 'atmosphaere_preview', methods: ['GET'])]
    public function index(): Response
    {
        $response = $this->render('Atmosphaere/preview.html.twig');
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }
}
