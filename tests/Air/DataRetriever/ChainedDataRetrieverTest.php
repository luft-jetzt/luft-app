<?php declare(strict_types=1);

namespace App\Tests\Air\DataRetriever;

use App\Air\DataRetriever\AdhocDataRetriever;
use App\Air\DataRetriever\ChainedDataRetriever;
use App\Air\DataRetriever\PostgisDataRetriever;
use App\Entity\Data;
use Caldera\GeoBasic\Coord\Coord;
use PHPUnit\Framework\TestCase;

class ChainedDataRetrieverTest extends TestCase
{
    public function testRetrieveDataForCoordCombinesResults(): void
    {
        $coord = new Coord(52.520008, 13.404954);

        $data1 = new Data();
        $data1->setValue(10.0);

        $data2 = new Data();
        $data2->setValue(20.0);

        $data3 = new Data();
        $data3->setValue(30.0);

        $postgisRetriever = $this->createMock(PostgisDataRetriever::class);
        $postgisRetriever
            ->expects($this->once())
            ->method('retrieveDataForCoord')
            ->with($coord)
            ->willReturn([$data1, $data2]);

        $adhocRetriever = $this->createMock(AdhocDataRetriever::class);
        $adhocRetriever
            ->expects($this->once())
            ->method('retrieveDataForCoord')
            ->with($coord)
            ->willReturn([$data3]);

        $chainedRetriever = new ChainedDataRetriever($postgisRetriever, $adhocRetriever);

        $result = $chainedRetriever->retrieveDataForCoord($coord);

        $this->assertCount(3, $result);
        $this->assertSame($data1, $result[0]);
        $this->assertSame($data2, $result[1]);
        $this->assertSame($data3, $result[2]);
    }

    public function testRetrieveDataForCoordWithEmptyResults(): void
    {
        $coord = new Coord(52.520008, 13.404954);

        $postgisRetriever = $this->createMock(PostgisDataRetriever::class);
        $postgisRetriever
            ->expects($this->once())
            ->method('retrieveDataForCoord')
            ->with($coord)
            ->willReturn([]);

        $adhocRetriever = $this->createMock(AdhocDataRetriever::class);
        $adhocRetriever
            ->expects($this->once())
            ->method('retrieveDataForCoord')
            ->with($coord)
            ->willReturn([]);

        $chainedRetriever = new ChainedDataRetriever($postgisRetriever, $adhocRetriever);

        $result = $chainedRetriever->retrieveDataForCoord($coord);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testRetrieveDataForCoordOnlyPostgisHasData(): void
    {
        $coord = new Coord(48.137154, 11.576124);

        $data1 = new Data();
        $data1->setValue(55.0);

        $postgisRetriever = $this->createMock(PostgisDataRetriever::class);
        $postgisRetriever
            ->method('retrieveDataForCoord')
            ->willReturn([$data1]);

        $adhocRetriever = $this->createMock(AdhocDataRetriever::class);
        $adhocRetriever
            ->method('retrieveDataForCoord')
            ->willReturn([]);

        $chainedRetriever = new ChainedDataRetriever($postgisRetriever, $adhocRetriever);

        $result = $chainedRetriever->retrieveDataForCoord($coord);

        $this->assertCount(1, $result);
        $this->assertSame($data1, $result[0]);
    }
}
