<?php declare(strict_types=1);

namespace App\SeoPage;

interface SeoPageInterface
{
    public function setTitle(string $title): SeoPageInterface;
    public function setDescription(string $description): SeoPageInterface;
    public function setCanonicalLink(string $link): SeoPageInterface;
    public function setStandardPreviewPhoto(): SeoPageInterface;
    public function setOpenGraphPreviewPhoto(string $assetUrl): SeoPageInterface;
    public function setTwitterPreviewPhoto(string $assetUrl): SeoPageInterface;
}
