<?php declare(strict_types=1);

namespace App\Controller;

use App\Air\SeoPage\SeoPage;
use App\Entity\City;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupCollectionInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class TemplateController extends AbstractController
{
    public function __construct(
        private readonly EntrypointLookupCollectionInterface $entrypointLookupCollection,
        ManagerRegistry $managerRegistry
    )
    {
        parent::__construct($managerRegistry);
    }

    public function cityListAction(): Response
    {
        return $this->render(
            'Template/city_list.html.twig', [
                'cityList' => $this->managerRegistry->getRepository(City::class)->findCitiesWithActiveStations(),
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
