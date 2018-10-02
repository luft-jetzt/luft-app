<?php declare(strict_types=1);

namespace App\SeoPage;

use App\StaticMap\UrlGenerator\UrlGeneratorInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;

class SeoPage
{
    /** @var SeoPageInterface */
    protected $sonataSeoPage;

    /** @var UrlGeneratorInterface $urlGenerator */
    protected $urlGenerator;

    public function __construct(SeoPageInterface $sonataSeoPage, UrlGeneratorInterface $urlGenerator)
    {
        $this->sonataSeoPage = $sonataSeoPage;
        $this->urlGenerator = $urlGenerator;
    }

    public function setTitle(string $title): SeoPage
    {
        $this->sonataSeoPage
            ->setTitle($title)
            ->addMeta('property', 'og:title', $title)
        ;

        return $this;
    }

    public function setDescription(string $description): SeoPage
    {
        $this->sonataSeoPage
            ->addMeta('name', 'description',$description)
            ->addMeta('property', 'og:description', $description)
        ;

        return $this;
    }

    public function setPreviewMap(StaticMapableInterface $staticMapable): SeoPageInterface
    {
        $this->sonataSeoPage
            ->addMeta('property', 'og:image', $this->urlGenerator->generate($staticMapable, 600, 315), ['escape' => false])
            ->addMeta('name', 'twitter:image', $this->urlGenerator->generate($staticMapable, 800, 320), ['escape' => false])
            ->addMeta('name', 'twitter:card', 'summary_large_image');

        return $this;
    }
    public function setCanonicalLink(string $link): SeoPage
    {
        $this->sonataSeoPage
            ->setLinkCanonical($link)
            ->addMeta('property', 'og:url', $link)
        ;

        return $this;
    }
}
