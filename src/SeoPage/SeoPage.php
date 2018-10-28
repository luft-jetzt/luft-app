<?php declare(strict_types=1);

namespace App\SeoPage;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;

class SeoPage extends AbstractSeoPage
{
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
        $package = new Package(new StaticVersionStrategy($this->assetsVersion));

        $this->sonataSeoPage
            ->addMeta('property', 'og:image', $package->getUrl('/img/share/opengraph.jpeg'))
            ->addMeta('name', 'twitter:image', $package->getUrl('/img/share/twitter.jpeg'))
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
