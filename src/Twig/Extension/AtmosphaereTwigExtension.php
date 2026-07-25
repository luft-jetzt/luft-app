<?php declare(strict_types=1);

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Helfer für die Neugestaltung „Atmosphäre": bildet die Gesamt-Belastungsstufe (0–6, aus
 * {@see PollutionLevelTwigExtension::maxPollutionLevel()}) auf die fünf Himmel-Zustände ab und
 * liefert das dazugehörige Urteil samt kurzer Empfehlung.
 *
 * Interne App-Skala: 0 = keine Daten (weiß), 1–2 grün, 3–4 gelb, 5–6 rot.
 * Atmosphäre-Himmel: 1 sehr gut · 2 gut · 3 mäßig · 4 schlecht · 5 sehr schlecht · 0 Idle.
 */
class AtmosphaereTwigExtension extends AbstractExtension
{
    private const array SKY_LEVEL = [0 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 3, 5 => 4, 6 => 5];

    private const array VERDICT = [
        0 => 'Keine Daten',
        1 => 'Sehr gut',
        2 => 'Gut',
        3 => 'Mäßig',
        4 => 'Schlecht',
        5 => 'Sehr schlecht',
    ];

    private const array SUBTITLE = [
        0 => 'Für diesen Ort liegen gerade keine aktuellen Messwerte vor.',
        1 => 'Beste Luft — unbeschwert draußen.',
        2 => 'Unbedenklich — genieß die frische Luft.',
        3 => 'Für Empfindliche: Anstrengung im Freien etwas reduzieren.',
        4 => 'Anstrengende Aktivitäten im Freien besser meiden.',
        5 => 'Draußen möglichst meiden und Fenster geschlossen halten.',
    ];

    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('at_sky_level', $this->skyLevel(...)),
            new TwigFunction('at_verdict', $this->verdict(...)),
            new TwigFunction('at_subtitle', $this->subtitle(...)),
            new TwigFunction('at_scale_position', $this->scalePosition(...)),
        ];
    }

    /** Interne Stufe 0–6 → Himmel-Zustand 0–5. */
    public function skyLevel(int $pollutionLevel): int
    {
        return self::SKY_LEVEL[max(0, min(6, $pollutionLevel))] ?? 0;
    }

    /** Urteil aus dem Himmel-Zustand (0–5). */
    public function verdict(int $skyLevel): string
    {
        return self::VERDICT[$skyLevel] ?? self::VERDICT[0];
    }

    public function subtitle(int $skyLevel): string
    {
        return self::SUBTITLE[$skyLevel] ?? self::SUBTITLE[0];
    }

    /** Position des Skalenmarkers in Prozent (Himmel-Zustand 1–5 → 10 %…90 %). */
    public function scalePosition(int $skyLevel): int
    {
        return match ($skyLevel) {
            1 => 10, 2 => 32, 3 => 52, 4 => 72, 5 => 92,
            default => 50,
        };
    }
}
