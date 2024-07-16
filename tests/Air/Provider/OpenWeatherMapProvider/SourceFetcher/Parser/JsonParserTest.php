<?php declare(strict_types=1);

namespace App\Tests\Air\Provider\OpenWeatherMapProvider\SourceFetcher\Parser;

use App\Air\Provider\OpenWeatherMapProvider\SourceFetcher\Parser\JsonParser;
use App\Air\Value\Value;
use PHPUnit\Framework\TestCase;

class JsonParserTest extends TestCase
{
    public function testUVValue(): void
    {
        $expectedResult = new Value();
        $expectedResult
            ->setValue(9.92)
            ->setDateTime(new \DateTime('2019-07-24 12:00:00', new \DateTimeZone('UTC')))
            ->setPollutant('uvindex');

        $testString = '{"lat":37.75,"lon":-122.37,"date_iso":"2019-07-24T12:00:00Z","date":1563969600,"value":9.92}';

        $parser = new JsonParser();
        $actualResult = $parser->parseUVIndex($testString);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testTemperature(): void
    {
        $expectedResult = new Value();
        $expectedResult
            ->setValue(285.514)
            ->setDateTime(new \DateTime('2017-01-30T16:16:07', new \DateTimeZone('UTC')))
            ->setPollutant('temperature');

        $testString = '{"coord":{"lon":139.01,"lat":35.02},"weather":[{"id":800,"main":"Clear","description":"clear sky","icon":"01n"}],"base":"stations","main":{"temp":285.514,"pressure":1013.75,"humidity":100,"temp_min":285.514,"temp_max":285.514,"sea_level":1023.22,"grnd_level":1013.75},"wind":{"speed":5.52,"deg":311},"clouds":{"all":0},"dt":1485792967,"sys":{"message":0.0025,"country":"JP","sunrise":1485726240,"sunset":1485763863},"id":1907296,"name":"Tawarano","cod":200}';

        $parser = new JsonParser();
        $actualResult = $parser->parseTemperature($testString);

        $this->assertEquals($expectedResult, $actualResult);
    }
}