<?php declare(strict_types=1);

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Verlinkt eine UV-Messstation auf ihre Tagesverlauf-Seite beim Bundesamt für Strahlenschutz (BfS),
 * das die UV-Messwerte liefert.
 *
 * Zuordnung station_code → BfS-Slug ist fest hinterlegt und am 25.07.2026 verifiziert (jede URL
 * liefert HTTP 200). Eine Ableitung aus dem Stationsnamen ist nicht zuverlässig, da die
 * luft.jetzt-Titel von den BfS-Namen abweichen (z. B. „Schneefernerhaus an der Zugspitze" → slug
 * `zugspitze`). Fulda (BFS678) hat keine BfS-Tagesverlauf-Seite und ist daher nicht enthalten.
 */
class BfsUvTwigExtension extends AbstractExtension
{
    private const string BASE_URL =
        'https://www.bfs.de/DE/themen/opt/uv/uv-index/aktuelle-tagesverlaeufe/_documents/%s_node.html';

    /** @var array<string, string> station_code => BfS-Slug */
    private const array STATION_SLUGS = [
        'BFS360' => 'andernach', 'BFS87' => 'osnabrueck', 'GAAHI746' => 'boesel', 'BFS912' => 'chieming',
        'GAAHI750' => 'cuxhaven', 'BAUA511' => 'dortmund', 'GAAHI404' => 'duderstadt', 'BFS382' => 'eckernfoerde',
        'BFS861' => 'fichtelberg', 'BFS59' => 'friedrichshafen', 'BFS363' => 'genthin', 'BFS378' => 'giessen',
        'BFS427' => 'groemitz', 'BFS11' => 'goerlitz', 'BFS864' => 'hamburg', 'DWD520' => 'hohenpeissenberg',
        'BFS328' => 'klippeneck', 'BFS762' => 'kulmbach', 'UBA833' => 'langen', 'DWD611' => 'lindenberg',
        'GAAHI658' => 'lueneburg', 'TROPOS140' => 'melpitz', 'BFS935' => 'muenchen', 'GAAHI768' => 'norderney',
        'BFS723' => 'salzgitter', 'BFS572' => 'kassel', 'UBA301' => 'schauinsland', 'BFS622' => 'zugspitze',
        'BFS885' => 'schweinfurt', 'BFS799' => 'stuttgart', 'BFS613' => 'sylt', 'BFS362' => 'tholey',
        'BFS478' => 'todendorf', 'BFS486' => 'waldhof', 'BFS375' => 'waldmuenchen', 'BFS161' => 'weissenburg',
        'GAAHI272' => 'wurmberg', 'UBA378' => 'zingst', 'BFS213' => 'zirchow',
    ];

    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('bfs_uv_station_url', $this->bfsUvStationUrl(...)),
        ];
    }

    /**
     * @return string|null Die BfS-Tagesverlauf-URL der Station oder null, wenn keine bekannt ist.
     */
    public function bfsUvStationUrl(?string $stationCode): ?string
    {
        if ($stationCode === null || !isset(self::STATION_SLUGS[$stationCode])) {
            return null;
        }

        return sprintf(self::BASE_URL, self::STATION_SLUGS[$stationCode]);
    }
}
