<?php declare(strict_types=1);

namespace App\Air\Map;

use App\Air\AirQuality\Calculator\AirQualityCalculatorInterface;
use App\Air\AirQuality\LevelCalculator\LevelCalculator;
use App\Air\Pollutant\AbstractPollutant;
use App\Air\Pollutant\PollutantInterface;
use App\Air\PollutantList\PollutantListInterface;
use App\Entity\Data;
use App\Twig\Extension\AtmosphaereTwigExtension;
use Symfony\Component\Routing\Exception\ExceptionInterface as RoutingExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Baut aus den Rohzeilen der Materialized View current_data
 * ({@see \App\Repository\DataRepository::findCurrentDataForMap()}) die GeoJSON-FeatureCollection
 * für die Deutschland-Übersichtskarte. Pro Station entsteht ein Feature; ohne Schadstoff-Filter
 * bestimmt der Schadstoff mit der höchsten Belastungsstufe Wert und Farbe des Markers.
 */
class MapStationFactory
{
    /**
     * Auf der Karte verfügbare Schadstoffe: Identifier → Pollutant-IDs in der Datenbank.
     * UVIndexMax wird wie in der PollutionDataFactory mit UVIndex zusammengeführt.
     * CO2 (Langzeitmessung, in der MV bis zu 14 Tage alt) bleibt bewusst außen vor.
     */
    public const array POLLUTANT_IDS = [
        'pm10' => [PollutantInterface::POLLUTANT_PM10],
        'pm25' => [PollutantInterface::POLLUTANT_PM25],
        'no2' => [PollutantInterface::POLLUTANT_NO2],
        'o3' => [PollutantInterface::POLLUTANT_O3],
        'so2' => [PollutantInterface::POLLUTANT_SO2],
        'co' => [PollutantInterface::POLLUTANT_CO],
        'uvindex' => [PollutantInterface::POLLUTANT_UVINDEX, PollutantInterface::POLLUTANT_UVINDEXMAX],
        'temperature' => [PollutantInterface::POLLUTANT_TEMPERATURE],
    ];

    public function __construct(
        protected readonly AirQualityCalculatorInterface $airQualityCalculator,
        protected readonly PollutantListInterface $pollutantList,
        protected readonly UrlGeneratorInterface $urlGenerator,
        protected readonly AtmosphaereTwigExtension $atmosphaereTwigExtension,
    )
    {
    }

    public function createFeatureCollection(array $rowList): array
    {
        $featureList = [];

        foreach ($this->groupRowsByStation($rowList) as $stationRowList) {
            $feature = $this->createFeature($stationRowList);

            if ($feature) {
                $featureList[] = $feature;
            }
        }

        return [
            'type' => 'FeatureCollection',
            'features' => $featureList,
        ];
    }

    protected function groupRowsByStation(array $rowList): array
    {
        $stationRowList = [];

        foreach ($rowList as $row) {
            $stationRowList[$row['stationCode']][] = $row;
        }

        return $stationRowList;
    }

    protected function createFeature(array $stationRowList): ?array
    {
        $maxLevel = 0;
        $maxRow = null;
        $maxIdentifier = null;

        foreach ($stationRowList as $row) {
            $pollutantIdentifier = $this->resolvePollutantIdentifier((int) $row['pollutant']);

            if (!$pollutantIdentifier) {
                continue;
            }

            $level = $this->calculateLevel($pollutantIdentifier, (float) $row['value']);

            if (!$maxRow || $level > $maxLevel) {
                $maxLevel = $level;
                $maxRow = $row;
                $maxIdentifier = $pollutantIdentifier;
            }
        }

        if (!$maxRow) {
            return null;
        }

        /** @var PollutantInterface $pollutant */
        $pollutant = $this->pollutantList->getPollutants()[$maxIdentifier];
        $stationCode = $maxRow['stationCode'];

        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    round((float) $maxRow['longitude'], 5),
                    round((float) $maxRow['latitude'], 5),
                ],
            ],
            'properties' => [
                'c' => $stationCode,
                'n' => $maxRow['title'] ?: $stationCode,
                'u' => $this->generateStationUrl($stationCode),
                'l' => $this->atmosphaereTwigExtension->skyLevel($maxLevel),
                'v' => $this->roundValue((float) $maxRow['value'], $pollutant),
                'un' => $pollutant->getUnitPlain(),
                'p' => $pollutant instanceof AbstractPollutant ? $pollutant->getShortName() : $pollutant->getIdentifier(),
                't' => $maxRow['dateTime']->getTimestamp(),
                'pr' => $this->resolveProvider($stationCode, $maxRow['provider']),
            ],
        ];
    }

    protected function resolvePollutantIdentifier(int $pollutantId): ?string
    {
        // UVIndexMax wird als UVIndex behandelt, vgl. PollutionDataFactory::getPollutantViewModelList()
        if (PollutantInterface::POLLUTANT_UVINDEXMAX === $pollutantId) {
            $pollutantId = PollutantInterface::POLLUTANT_UVINDEX;
        }

        foreach (self::POLLUTANT_IDS as $identifier => $idList) {
            if (in_array($pollutantId, $idList, true)) {
                return $identifier;
            }
        }

        return null;
    }

    protected function calculateLevel(string $pollutantIdentifier, float $value): int
    {
        $pollutionLevel = $this->airQualityCalculator->getPollutionLevels()[$pollutantIdentifier];

        return LevelCalculator::calculate($pollutionLevel, (new Data())->setValue($value));
    }

    protected function roundValue(float $value, PollutantInterface $pollutant): float|int
    {
        $decimals = $pollutant instanceof AbstractPollutant ? $pollutant->getDecimals() : 0;

        if (0 === $decimals) {
            return (int) round($value);
        }

        return round($value, $decimals);
    }

    protected function generateStationUrl(string $stationCode): string
    {
        try {
            return $this->urlGenerator->generate('station', ['stationCode' => $stationCode]);
        } catch (RoutingExceptionInterface) {
            // Stationscodes, die nicht auf das Routen-Requirement passen, verlinken direkt
            return sprintf('/%s', rawurlencode($stationCode));
        }
    }

    protected function resolveProvider(string $stationCode, ?string $provider): ?string
    {
        // BfS-Stationen (UV-Index) haben keinen provider-Wert, sind aber am Präfix erkennbar
        // (Präfix-Liste identisch zum officialOnly-Filter in DataRepository::findCurrentDataForMap)
        if (!$provider && preg_match('/^(BFS|UBA|GAAHI|DWD|TROPOS|IFA|BAUA)/', $stationCode)) {
            return 'bfs';
        }

        return $provider;
    }
}
