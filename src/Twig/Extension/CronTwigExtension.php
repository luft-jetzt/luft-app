<?php declare(strict_types=1);

namespace App\Twig\Extension;

use Cron\CronExpression;

class CronTwigExtension extends \Twig_Extension
{
    public function __construct()
    {
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('cron', [$this, 'cron'], ['is_safe' => ['raw']]),
        ];
    }

    public function cron(string $cronString, string $dateFormat): string
    {
        $cron = CronExpression::factory($cronString);

        return $cron->getPreviousRunDate()->format($dateFormat);
    }

    public function getName(): string
    {
        return 'cron_extension';
    }
}

