<?php declare(strict_types=1);

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['prod' => true, 'dev' => true, 'test' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true],
    Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
    Nelmio\ApiDocBundle\NelmioApiDocBundle::class => ['all' => true],
    Sonata\SeoBundle\SonataSeoBundle::class => ['all' => true],
    JMS\SerializerBundle\JMSSerializerBundle::class => ['all' => true],
    FOS\JsRoutingBundle\FOSJsRoutingBundle::class => ['all' => true],
    Http\HttplugBundle\HttplugBundle::class => ['all' => true],
    FOS\ElasticaBundle\FOSElasticaBundle::class => ['all' => true],
    Fresh\DoctrineEnumBundle\FreshDoctrineEnumBundle::class => ['all' => true],
    Presta\SitemapBundle\PrestaSitemapBundle::class => ['all' => true],
    Flagception\Bundle\FlagceptionBundle\FlagceptionBundle::class => ['all' => true],
    Symfony\WebpackEncoreBundle\WebpackEncoreBundle::class => ['all' => true],
    Bazinga\GeocoderBundle\BazingaGeocoderBundle::class => ['all' => true],
    OldSound\RabbitMqBundle\OldSoundRabbitMqBundle::class => ['all' => true],
    MalteHuebner\ImpressBundle\MalteHuebnerImpressBundle::class => ['all' => true],
    Twig\Extra\TwigExtraBundle\TwigExtraBundle::class => ['all' => true],
    WhiteOctober\BreadcrumbsBundle\WhiteOctoberBreadcrumbsBundle::class => ['all' => true],
    Sonata\Doctrine\Bridge\Symfony\SonataDoctrineBundle::class => ['all' => true],
    Sonata\Form\Bridge\Symfony\SonataFormBundle::class => ['all' => true],
    Sonata\BlockBundle\SonataBlockBundle::class => ['all' => true],
];
