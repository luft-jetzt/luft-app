<?php declare(strict_types=1);

namespace App\SeoPage;

use Sonata\SeoBundle\Seo\SeoPageInterface as SonataSeoPageInterface;

abstract class AbstractSeoPage implements SeoPageInterface
{
    /** @var SonataSeoPageInterface $sonataSeoPage*/
    protected $sonataSeoPage;

    /** @var string $assetsVersion */
    protected $assetsVersion;

    public function __construct(SonataSeoPageInterface $sonataSeoPage, string $assetsVersion)
    {
        $this->sonataSeoPage = $sonataSeoPage;
        $this->assetsVersion = $assetsVersion;
    }
}
