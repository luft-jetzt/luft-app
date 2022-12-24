<?php declare(strict_types=1);

namespace App\Controller;

use App\SeoPage\SeoPage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontpageController extends AbstractController
{
    #[Route('/', name: 'frontpage', options: ['expose' => true], priority: 403)]
    public function indexAction(SeoPage $seoPage): Response
    {
        $seoPage
            ->setStandardPreviewPhoto()
            ->setTitle('Aktuelle Schadstoffwerte aus Luftmessstationen in deiner Umgebung: Stickstoffdioxid, Feinstaub und Ozon')
            ->setDescription('Luft.jetzt zeigt dir aktuelle Messwerte aus Luftmessstationen deiner Umgebung. Informiere dich über Stickstoffdioxid, Feinstaub und Ozon');;

        return $this->render('Default/select.html.twig');
    }
}
