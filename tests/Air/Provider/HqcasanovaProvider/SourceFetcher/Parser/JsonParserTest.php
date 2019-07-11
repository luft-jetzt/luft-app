<?php declare(strict_types=1);

namespace App\Tests\Air\Provider\HqcasanovaProvider\SourceFetcher\Parser;

use App\Air\Measurement\MeasurementInterface;
use App\Pollution\Value\Value;
use App\Provider\HqcasanovaProvider\SourceFetcher\Parser\JsonParser;
use PHPUnit\Framework\TestCase;

class JsonParserTest extends TestCase
{
    public function testParser(): void
    {
        $data = 'process({"0":"413.38","1":"409.57","10":"388.63","units":"ppm","date":"2019-07-10T13:00:59+02:00","delta":5.38,"all":"Up-to-date weekly average CO2 at Mauna Loa\nWeek starting on June 30, 2019: 413.38 ppm\nWeekly value from 1 year ago: 409.57 ppm\nWeekly value from 10 years ago: 388.63 ppm"})';

        $parser = new JsonParser();
        $valueList = $parser->parse($data);

        $expectedValue = new Value();
        $expectedValue
            ->setStation('USHIMALO')
            ->setValue(413.38)
            ->setDateTime(new \DateTime('2019-07-10T13:00:59+02:00'))
            ->setPollutant(MeasurementInterface::MEASUREMENT_CO2);

        $this->assertCount(1, $valueList);

        $actualValue = array_pop($valueList);

        $this->assertEquals($expectedValue, $actualValue);

    }
}