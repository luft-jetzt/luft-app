<?php declare(strict_types=1);

namespace App\Twig\Extension;

use Cron\CronExpression;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CronTwigExtension extends AbstractExtension
{
    public function __construct()
    {
    }

    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('cron', $this->cron(...), ['is_safe' => ['raw']]),
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

