<?php declare(strict_types=1);

namespace App\Tests\Air\SourceFetcher;

use App\Air\SourceFetcher\FetchResult;
use PHPUnit\Framework\TestCase;

class FetchResultTest extends TestCase
{
    public function testIncCounter(): void
    {
        $fetchResult = new FetchResult();

        $result = $fetchResult->incCounter('pm10');

        $this->assertSame($fetchResult, $result);
        $this->assertEquals(['pm10' => 1], $fetchResult->getCounters());
    }

    public function testIncCounterWithStep(): void
    {
        $fetchResult = new FetchResult();

        $fetchResult->incCounter('pm10', 5);

        $this->assertEquals(['pm10' => 5], $fetchResult->getCounters());
    }

    public function testIncCounterAccumulates(): void
    {
        $fetchResult = new FetchResult();

        $fetchResult
            ->incCounter('pm10', 3)
            ->incCounter('pm10', 2)
            ->incCounter('pm10');

        $this->assertEquals(['pm10' => 6], $fetchResult->getCounters());
    }

    public function testSetCounter(): void
    {
        $fetchResult = new FetchResult();

        $result = $fetchResult->setCounter('no2', 42);

        $this->assertSame($fetchResult, $result);
        $this->assertEquals(['no2' => 42], $fetchResult->getCounters());
    }

    public function testSetCounterOverwrites(): void
    {
        $fetchResult = new FetchResult();

        $fetchResult
            ->incCounter('pm10', 10)
            ->setCounter('pm10', 5);

        $this->assertEquals(['pm10' => 5], $fetchResult->getCounters());
    }

    public function testMultipleCounters(): void
    {
        $fetchResult = new FetchResult();

        $fetchResult
            ->incCounter('pm10', 10)
            ->incCounter('no2', 20)
            ->incCounter('o3', 30);

        $counters = $fetchResult->getCounters();

        $this->assertCount(3, $counters);
        $this->assertEquals(10, $counters['pm10']);
        $this->assertEquals(20, $counters['no2']);
        $this->assertEquals(30, $counters['o3']);
    }

    public function testGetCountersEmptyByDefault(): void
    {
        $fetchResult = new FetchResult();

        $this->assertEmpty($fetchResult->getCounters());
    }

    public function testFluentInterface(): void
    {
        $fetchResult = new FetchResult();

        $result = $fetchResult
            ->incCounter('a')
            ->incCounter('b')
            ->setCounter('c', 100);

        $this->assertSame($fetchResult, $result);
    }
}
