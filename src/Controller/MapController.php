<?php declare(strict_types=1);

namespace App\Controller;

use App\Air\SeoPage\SeoPage;
use Symfony\Component\HttpFoundation\Response;

class MapController extends AbstractController
{
    /**
     * Schadstoff-Chips der Übersichtskarte: identifier = API-Parameter `pollutant`
     * von /api/map/stations.geojson, Label = Anzeige im Chip.
     */
    private const array POLLUTANT_CHIPS = [
        ['identifier' => 'all', 'label' => 'Gesamtindex'],
        ['identifier' => 'pm10', 'label' => 'PM10'],
        ['identifier' => 'pm25', 'label' => 'PM2,5'],
        ['identifier' => 'no2', 'label' => 'NO₂'],
        ['identifier' => 'o3', 'label' => 'O₃'],
        ['identifier' => 'so2', 'label' => 'SO₂'],
        ['identifier' => 'co', 'label' => 'CO'],
        ['identifier' => 'uvindex', 'label' => 'UV-Index'],
        ['identifier' => 'temperature', 'label' => 'Temperatur'],
    ];

    public function indexAction(SeoPage $seoPage): Response
    {
        $seoPage
            ->setStandardPreviewPhoto()
            ->setTitle('Luftqualitäts-Karte Deutschland — luft.jetzt')
            ->setDescription('Alle Luftmessstationen auf einer Karte: Feinstaub, Stickstoffdioxid, Ozon, UV-Index und Temperatur in Echtzeit — amtliche Stationen und Sensor.Community.');

        return $this->render('Atmosphaere/map.html.twig', [
            'pollutants' => self::POLLUTANT_CHIPS,
            // Legenden-Stufen (Himmel-Zustand 1–5); Labels liefert at_verdict() im Template.
            'legendLevels' => range(1, 5),
        ]);
    }
}
