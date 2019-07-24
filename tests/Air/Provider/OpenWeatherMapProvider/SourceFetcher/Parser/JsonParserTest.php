<?php declare(strict_types=1);

namespace App\Tests\Air\Provider\OpenWeatherMapProvider\SourceFetcher\Parser;

use App\Air\Measurement\MeasurementInterface;
use App\Pollution\Value\Value;
use App\Provider\OpenWeatherMapProvider\SourceFetcher\Parser\JsonParser;
use PHPUnit\Framework\TestCase;

class JsonParserTest extends TestCase
{
    public function testFoo(): void
    {
        $value = new Value();
        $value
            ->setValue(9.92)
            ->setDateTime(new \DateTime('2019-07-24 12:00:00', new \DateTimeZone('UTC')))
            ->setPollutant(MeasurementInterface::MEASUREMENT_UV);

        $expectedResult = [
            $value,
        ];

        $testString = '{"lat":37.75,"lon":-122.37,"date_iso":"2019-07-24T12:00:00Z","date":1563969600,"value":9.92}';

        $parser = new JsonParser();
        $actualResult = $parser->parse($testString);

        $this->assertEquals($expectedResult, $actualResult);
    }
}