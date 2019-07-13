<?php declare(strict_types=1);

namespace App\SeoPage;

use App\StaticMap\UrlGenerator\UrlGeneratorInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;

class SeoPage extends AbstractSeoPage
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

    public function setTitle(string $title): SeoPageInterface
    {
        $this->sonataSeoPage
            ->setTitle($title)
            ->addMeta('property', 'og:title', $title);

        return $this;
    }

    public function setDescription(string $description): SeoPageInterface
    {
        $this->sonataSeoPage
            ->addMeta('name', 'description', $description)
            ->addMeta('property', 'og:description', $description);

        return $this;
    }

    public function setStandardPreviewPhoto(): SeoPageInterface
    {
        $this->sonataSeoPage
            ->addMeta('property', 'og:image', $this->asset('/img/share/opengraph.jpeg'))
            ->addMeta('name', 'twitter:image', $this->asset('/img/share/twitter.jpeg'))
            ->addMeta('name', 'twitter:card', 'summary_large_image');

        return $this;
    }

    public function setPreviewMap(StaticMapableInterface $staticMapable): SeoPage
    public function setOpenGraphPreviewPhoto(string $assetUrl): SeoPageInterface
    {
        $this->sonataSeoPage->addMeta('property', 'og:image', $this->asset($assetUrl));

        return $this;
    }

    public function setTwitterPreviewPhoto(string $assetUrl): SeoPageInterface
    {
        $this->sonataSeoPage
            ->addMeta('property', 'og:image', $this->urlGenerator->generate($staticMapable, 600, 315),
                ['escape' => false])
            ->addMeta('name', 'twitter:image', $this->urlGenerator->generate($staticMapable, 800, 320),
                ['escape' => false])
            ->addMeta('name', 'twitter:card', 'summary_large_image');
            ->addMeta('name', 'twitter:image', $this->asset($assetUrl))
            ->addMeta('name', 'twitter:card', 'summary_large_image');

        return $this;
    }

    public function setCanonicalLink(string $link): SeoPageInterface
    {
        $this->sonataSeoPage
            ->setLinkCanonical($link)
            ->addMeta('property', 'og:url', $link);

        return $this;
    }
}
