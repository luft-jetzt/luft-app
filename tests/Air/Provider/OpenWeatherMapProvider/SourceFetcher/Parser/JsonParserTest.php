<?php declare(strict_types=1);

namespace App\Tests\Air\Provider\OpenWeatherMapProvider\SourceFetcher\Parser;

use App\Air\Provider\OpenWeatherMapProvider\SourceFetcher\Parser\JsonParser;
use Caldera\LuftModel\Model\Value;
use PHPUnit\Framework\TestCase;

class JsonParserTest extends TestCase
{
    private JsonParser $parser;

    protected function setUp(): void
    {
        $this->parser = new JsonParser();
    }

    public function testUVValue(): void
    {
        $expectedResult = new Value();
        $expectedResult
            ->setValue(9.92)
            ->setDateTime(new \DateTime('2019-07-24 12:00:00', new \DateTimeZone('UTC')))
            ->setPollutant('uvindex');

        $testString = '{"lat":37.75,"lon":-122.37,"date_iso":"2019-07-24T12:00:00Z","date":1563969600,"value":9.92}';

        $actualResult = $this->parser->parseUVIndex($testString);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testUVValueWithLowIndex(): void
    {
        $testString = '{"lat":52.52,"lon":13.40,"date_iso":"2024-01-15T10:00:00Z","date":1705316400,"value":1.5}';

        $result = $this->parser->parseUVIndex($testString);

        $this->assertEquals(1.5, $result->getValue());
        $this->assertEquals('uvindex', $result->getPollutant());
    }

    public function testUVValueWithZeroIndex(): void
    {
        $testString = '{"lat":52.52,"lon":13.40,"date_iso":"2024-01-15T22:00:00Z","date":1705359600,"value":0}';

        $result = $this->parser->parseUVIndex($testString);

        $this->assertEquals(0.0, $result->getValue());
    }

    public function testTemperature(): void
    {
        $expectedResult = new Value();
        $expectedResult
            ->setValue(285.514)
            ->setDateTime(new \DateTime('2017-01-30T16:16:07', new \DateTimeZone('UTC')))
            ->setPollutant('temperature');

        $testString = '{"coord":{"lon":139.01,"lat":35.02},"weather":[{"id":800,"main":"Clear","description":"clear sky","icon":"01n"}],"base":"stations","main":{"temp":285.514,"pressure":1013.75,"humidity":100,"temp_min":285.514,"temp_max":285.514,"sea_level":1023.22,"grnd_level":1013.75},"wind":{"speed":5.52,"deg":311},"clouds":{"all":0},"dt":1485792967,"sys":{"message":0.0025,"country":"JP","sunrise":1485726240,"sunset":1485763863},"id":1907296,"name":"Tawarano","cod":200}';

        $actualResult = $this->parser->parseTemperature($testString);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testTemperatureWithNegativeValue(): void
    {
        $testString = '{"main":{"temp":-5.5},"dt":1705316400}';

        $result = $this->parser->parseTemperature($testString);

        $this->assertEquals(-5.5, $result->getValue());
        $this->assertEquals('temperature', $result->getPollutant());
    }

    public function testParseUVIndexWithInvalidJsonThrowsException(): void
    {
        $this->expectException(\JsonException::class);

        $this->parser->parseUVIndex('invalid json');
    }

    public function testParseTemperatureWithInvalidJsonThrowsException(): void
    {
        $this->expectException(\JsonException::class);

        $this->parser->parseTemperature('invalid json');
    }

    public function testParsedValueHasCorrectPollutantType(): void
    {
        $uvJson = '{"date":1705316400,"value":5.0}';
        $tempJson = '{"main":{"temp":293.15},"dt":1705316400}';

        $uvResult = $this->parser->parseUVIndex($uvJson);
        $tempResult = $this->parser->parseTemperature($tempJson);

        $this->assertEquals('uvindex', $uvResult->getPollutant());
        $this->assertEquals('temperature', $tempResult->getPollutant());
    }

    public function testDateTimeIsInUtc(): void
    {
        $testString = '{"date":1705316400,"value":5.0}';

        $result = $this->parser->parseUVIndex($testString);
        $dateTime = $result->getDateTime();

        // Timezone can be 'UTC' or '+00:00' depending on PHP version
        $this->assertContains($dateTime->getTimezone()->getName(), ['UTC', '+00:00']);
    }
}
