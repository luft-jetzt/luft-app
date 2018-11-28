<?php declare(strict_types=1);

namespace App\Controller;

use App\SeoPage\SeoPage;
use Symfony\Component\HttpFoundation\Response;

class FrontpageController extends AbstractController
{
    public function indexAction(SeoPage $seoPage): Response
    {
        $seoPage->setStandardPreviewPhoto();

        return $this->render('Default/select.html.twig');
    }
}
