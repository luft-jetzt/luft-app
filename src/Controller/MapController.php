<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class MapController extends AbstractController
{
    public function mapAction(): Response
    {
        return $this->render('Map/map.html.twig', []);
    }

}
