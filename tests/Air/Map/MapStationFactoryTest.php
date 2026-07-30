<?php declare(strict_types=1);

namespace App\Tests\Air\Map;

use App\Air\AirQuality\Calculator\AirQualityCalculator;
use App\Air\AirQuality\PollutionLevel\COLevel;
use App\Air\AirQuality\PollutionLevel\NO2Level;
use App\Air\AirQuality\PollutionLevel\O3Level;
use App\Air\AirQuality\PollutionLevel\PM10Level;
use App\Air\AirQuality\PollutionLevel\PM25Level;
use App\Air\AirQuality\PollutionLevel\SO2Level;
use App\Air\AirQuality\PollutionLevel\TemperatureLevel;
use App\Air\AirQuality\PollutionLevel\UVIndexLevel;
use App\Air\Map\MapStationFactory;
use App\Air\Pollutant\CO;
use App\Air\Pollutant\NO2;
use App\Air\Pollutant\O3;
use App\Air\Pollutant\PM10;
use App\Air\Pollutant\PM25;
use App\Air\Pollutant\PollutantInterface;
use App\Air\Pollutant\SO2;
use App\Air\Pollutant\Temperature;
use App\Air\Pollutant\UVIndex;
use App\Air\PollutantList\PollutantList;
use App\Twig\Extension\AtmosphaereTwigExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MapStationFactoryTest extends TestCase
{
    public function testEmptyRowListYieldsEmptyFeatureCollection(): void
    {
        $featureCollection = $this->createFactory()->createFeatureCollection([]);

        $this->assertEquals(['type' => 'FeatureCollection', 'features' => []], $featureCollection);
    }

    public function testFeatureForSinglePollutant(): void
    {
        $row = $this->createRow('DENI200', PollutantInterface::POLLUTANT_PM10, 42.4);

        $featureCollection = $this->createFactory()->createFeatureCollection([$row]);

        $this->assertCount(1, $featureCollection['features']);

        $feature = $featureCollection['features'][0];

        $this->assertEquals('Feature', $feature['type']);
        $this->assertEquals('Point', $feature['geometry']['type']);
        $this->assertEquals('DENI200', $feature['properties']['c']);
        $this->assertEquals('Teststation', $feature['properties']['n']);
        $this->assertEquals('/DENI200', $feature['properties']['u']);
        $this->assertSame(3, $feature['properties']['l']); // PM10 42,4 → Stufe 4 → Himmel 3
        $this->assertSame(42, $feature['properties']['v']); // PM10 hat 0 Dezimalstellen
        $this->assertEquals('µg/m³', $feature['properties']['un']);
        $this->assertEquals('PM10', $feature['properties']['p']);
        $this->assertSame($row['dateTime']->getTimestamp(), $feature['properties']['t']);
        $this->assertEquals('uba_de', $feature['properties']['pr']);
    }

    public function testMaxLevelPollutantDeterminesFeature(): void
    {
        $rowList = [
            $this->createRow('DENI200', PollutantInterface::POLLUTANT_PM10, 5), // Stufe 1
            $this->createRow('DENI200', PollutantInterface::POLLUTANT_NO2, 300), // Stufe 5
            $this->createRow('DENI200', PollutantInterface::POLLUTANT_O3, 70), // Stufe 3
        ];

        $featureCollection = $this->createFactory()->createFeatureCollection($rowList);

        $this->assertCount(1, $featureCollection['features']);

        $feature = $featureCollection['features'][0];

        $this->assertSame(4, $feature['properties']['l']); // Stufe 5 → Himmel 4
        $this->assertEquals('NO2', $feature['properties']['p']);
        $this->assertSame(300, $feature['properties']['v']);
    }

    public function testSkyLevelMapping(): void
    {
        $factory = $this->createFactory();

        // PM10-Stufen 1–5 → Himmel 1, 2, 3, 3, 4
        $expectedSkyLevelList = ['5.0' => 1, '15.0' => 2, '25.0' => 3, '42.0' => 3, '150.0' => 4];

        foreach ($expectedSkyLevelList as $value => $expectedSkyLevel) {
            $featureCollection = $factory->createFeatureCollection([$this->createRow('DENI200', PollutantInterface::POLLUTANT_PM10, (float) $value)]);

            $this->assertSame($expectedSkyLevel, $featureCollection['features'][0]['properties']['l'], sprintf('PM10 value %s', $value));
        }
    }

    public function testUVIndexMaxIsMergedIntoUVIndex(): void
    {
        $row = $this->createRow('BFSUV123', PollutantInterface::POLLUTANT_UVINDEXMAX, 6.44, provider: null);

        $featureCollection = $this->createFactory()->createFeatureCollection([$row]);

        $feature = $featureCollection['features'][0];

        $this->assertEquals('UVIndex', $feature['properties']['p']);
        $this->assertSame(6.4, $feature['properties']['v']); // UV-Index hat 1 Dezimalstelle
        $this->assertSame(3, $feature['properties']['l']);
        $this->assertEquals('', $feature['properties']['un']);
        $this->assertEquals('bfs', $feature['properties']['pr']); // BfS-Präfix ohne provider-Wert
    }

    public function testUnknownPollutantIsSkipped(): void
    {
        $rowList = [
            $this->createRow('DENI200', PollutantInterface::POLLUTANT_CO2, 400),
        ];

        $featureCollection = $this->createFactory()->createFeatureCollection($rowList);

        $this->assertCount(0, $featureCollection['features']);
    }

    public function testCoordinatesAreRounded(): void
    {
        $row = $this->createRow('DENI200', PollutantInterface::POLLUTANT_PM10, 12, latitude: 53.1234567, longitude: 10.9876543);

        $featureCollection = $this->createFactory()->createFeatureCollection([$row]);

        $this->assertEquals([10.98765, 53.12346], $featureCollection['features'][0]['geometry']['coordinates']);
    }

    public function testTitleFallsBackToStationCode(): void
    {
        $row = $this->createRow('DENI200', PollutantInterface::POLLUTANT_PM10, 12, title: null);

        $featureCollection = $this->createFactory()->createFeatureCollection([$row]);

        $this->assertEquals('DENI200', $featureCollection['features'][0]['properties']['n']);
    }

    public function testStationUrlFallbackForUnroutableStationCode(): void
    {
        $urlGenerator = $this->createStub(UrlGeneratorInterface::class);
        $urlGenerator
            ->method('generate')
            ->willThrowException(new InvalidParameterException('does not match requirement'))
        ;

        $featureCollection = $this
            ->createFactory($urlGenerator)
            ->createFeatureCollection([$this->createRow('DENI200', PollutantInterface::POLLUTANT_PM10, 12)])
        ;

        $this->assertEquals('/DENI200', $featureCollection['features'][0]['properties']['u']);
    }

    public function testMultipleStationsYieldMultipleFeatures(): void
    {
        $rowList = [
            $this->createRow('DENI200', PollutantInterface::POLLUTANT_PM10, 12),
            $this->createRow('DEHH001', PollutantInterface::POLLUTANT_TEMPERATURE, 24.6),
        ];

        $featureCollection = $this->createFactory()->createFeatureCollection($rowList);

        $this->assertCount(2, $featureCollection['features']);

        $temperatureFeature = $featureCollection['features'][1];

        $this->assertEquals('DEHH001', $temperatureFeature['properties']['c']);
        $this->assertEquals('Temperature', $temperatureFeature['properties']['p']);
        $this->assertSame(25, $temperatureFeature['properties']['v']);
        $this->assertEquals('°C', $temperatureFeature['properties']['un']);
        $this->assertSame(2, $temperatureFeature['properties']['l']);
    }

    protected function createFactory(?UrlGeneratorInterface $urlGenerator = null): MapStationFactory
    {
        $airQualityCalculator = new AirQualityCalculator();
        $airQualityCalculator
            ->addPollutionLevel(new PM10Level())
            ->addPollutionLevel(new PM25Level())
            ->addPollutionLevel(new NO2Level())
            ->addPollutionLevel(new O3Level())
            ->addPollutionLevel(new SO2Level())
            ->addPollutionLevel(new COLevel())
            ->addPollutionLevel(new UVIndexLevel())
            ->addPollutionLevel(new TemperatureLevel())
        ;

        $pollutantList = new PollutantList();
        $pollutantList
            ->addPollutant(new PM10())
            ->addPollutant(new PM25())
            ->addPollutant(new NO2())
            ->addPollutant(new O3())
            ->addPollutant(new SO2())
            ->addPollutant(new CO())
            ->addPollutant(new UVIndex())
            ->addPollutant(new Temperature())
        ;

        if (!$urlGenerator) {
            $urlGenerator = $this->createStub(UrlGeneratorInterface::class);
            $urlGenerator
                ->method('generate')
                ->willReturnCallback(fn (string $name, array $parameters = []): string => sprintf('/%s', $parameters['stationCode']))
            ;
        }

        return new MapStationFactory($airQualityCalculator, $pollutantList, $urlGenerator, new AtmosphaereTwigExtension());
    }

    protected function createRow(string $stationCode, int $pollutant, float $value, ?string $title = 'Teststation', float $latitude = 53.45, float $longitude = 10.23, ?string $provider = 'uba_de'): array
    {
        return [
            'stationCode' => $stationCode,
            'title' => $title,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'pollutant' => $pollutant,
            'value' => $value,
            'dateTime' => new \DateTime('2026-07-30 10:00:00', new \DateTimeZone('UTC')),
            'provider' => $provider,
        ];
    }
}
