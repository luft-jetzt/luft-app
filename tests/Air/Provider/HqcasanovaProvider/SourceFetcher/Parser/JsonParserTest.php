<?php declare(strict_types=1);

namespace App\Tests\Air\Provider\HqcasanovaProvider\SourceFetcher\Parser;

use App\Provider\HqcasanovaProvider\SourceFetcher\Parser\JsonParser;
use PHPUnit\Framework\TestCase;

class JsonParserTest extends TestCase
{
    public function testFoo(): void
    {
        $data = 'process({"0":"413.38","1":"409.57","10":"388.63","units":"ppm","date":"2019-07-10T13:00:59+02:00","delta":5.38,"all":"Up-to-date weekly average CO2 at Mauna Loa\nWeek starting on June 30, 2019: 413.38 ppm\nWeekly value from 1 year ago: 409.57 ppm\nWeekly value from 10 years ago: 388.63 ppm"})';

        $parser = new JsonParser();
        $parser->parse($data);
    }
}