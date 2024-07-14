<?php declare(strict_types=1);

namespace App\Air\SeoPage;

class SeoPage extends AbstractSeoPage
{
    #[\Override]
    public function setTitle(string $title): SeoPageInterface
    {
        $this->sonataSeoPage
            ->setTitle($title)
            ->addMeta('property', 'og:title', $title);

        return $this;
    }

    #[\Override]
    public function setDescription(string $description): SeoPageInterface
    {
        $this->sonataSeoPage
            ->addMeta('name', 'description', $description)
            ->addMeta('property', 'og:description', $description);

        return $this;
    }

    #[\Override]
    public function setStandardPreviewPhoto(): SeoPageInterface
    {
        $this->sonataSeoPage
            ->addMeta('property', 'og:image', $this->asset('/img/share/opengraph.jpeg'))
            ->addMeta('name', 'twitter:image', $this->asset('/img/share/twitter.jpeg'))
            ->addMeta('name', 'twitter:card', 'summary_large_image');

        return $this;
    }

    #[\Override]
    public function setOpenGraphPreviewPhoto(string $assetUrl): SeoPageInterface
    {
        $this->sonataSeoPage->addMeta('property', 'og:image', $this->asset($assetUrl));

        return $this;
    }

    #[\Override]
    public function setTwitterPreviewPhoto(string $assetUrl): SeoPageInterface
    {
        $this->sonataSeoPage
            ->addMeta('name', 'twitter:image', $this->asset($assetUrl))
            ->addMeta('name', 'twitter:card', 'summary_large_image');

        return $this;
    }

    #[\Override]
    public function setCanonicalLink(string $link): SeoPageInterface
    {
        $this->sonataSeoPage
            ->setLinkCanonical($link)
            ->addMeta('property', 'og:url', $link);

        return $this;
    }
}
