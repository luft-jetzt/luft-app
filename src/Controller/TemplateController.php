<?php declare(strict_types=1);

namespace App\Controller;

use App\Air\SeoPage\SeoPage;
use App\Entity\City;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class TemplateController extends AbstractController
{
    public function cityListAction(): Response
    {
        return $this->render(
            'Template/city_list.html.twig', [
                'cityList' => $this->managerRegistry->getRepository(City::class)->findCitiesWithActiveStations(),
            ]
        );
    }

    public function staticAction(string $templateName, string $title, Breadcrumbs $breadcrumbs, RouterInterface $router, SeoPage $seoPage): Response
    {
        $templateFilename = sprintf('Static/%s.html.twig', $templateName);

        $seoPage->setTitle($title);

        $breadcrumbs
            ->addItem('Luft', $router->generate('display'))
            ->addItem($title);

        return $this->render($templateFilename);
    }
}
