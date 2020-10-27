<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\SeoPage\SeoPage;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupCollectionInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class TemplateController extends AbstractController
{
    protected EntrypointLookupCollectionInterface $entrypointLookupCollection;

    public function __construct(EntrypointLookupCollectionInterface $entrypointLookupCollection)
    {
        $this->entrypointLookupCollection = $entrypointLookupCollection;
    }

    public function cityListAction(): Response
    {
        return $this->render(
            'Template/city_list.html.twig', [
                'cityList' => $this->getDoctrine()->getRepository(City::class)->findCitiesWithActiveStations(),
            ]
        );
    }

    public function staticAction(string $templateName, Breadcrumbs $breadcrumbs, RouterInterface $router, SeoPage $seoPage): Response
    {
        $title = $this->readH2Tag($templateName);

        $templateFilename = sprintf('Static/%s.html.twig', $templateName);

        $seoPage->setTitle($title);

        $breadcrumbs
            ->addItem('Luft', $router->generate('display'))
            ->addItem($title);

        return $this->render($templateFilename);
    }

    protected function readH2Tag(string $templateName): ?string
    {
        $templateFilename = sprintf('Static/%s.html.twig', $templateName);

        $templateContent = $this->renderView($templateFilename);

        $crawler = new Crawler($templateContent);
        $h2 = $crawler->filter('h2')->first();

        /** @see https://github.com/symfony/webpack-encore-bundle/issues/73#issuecomment-514649426 */
        $this->entrypointLookupCollection->getEntrypointLookup()->reset();

        return $h2 ? $h2->text() : null;
    }
}
