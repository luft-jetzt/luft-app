<?php declare(strict_types=1);

namespace App\Tests\Air\PollutantList;

use App\Air\Pollutant\CO;
use App\Air\Pollutant\NO2;
use App\Air\Pollutant\O3;
use App\Air\Pollutant\SO2;
use App\Air\PollutantList\PollutantList;
use PHPUnit\Framework\TestCase;

class PollutantListTest extends TestCase
{
    public function testEmptyList(): void
    {
        $this->assertCount(0, (new PollutantList())->getPollutants());
    }

    public function testListWithTwoElements(): void
    {
        $pollutantList = new PollutantList();

        $pollutantList
            ->addPollutant(new CO())
            ->addPollutant(new NO2());

        $this->assertCount(2, $pollutantList->getPollutants());
    }

    public function testListContentWithTwoElements(): void
    {
        $pollutantList = new PollutantList();

        $pollutantList
            ->addPollutant(new CO())
            ->addPollutant(new NO2());

        $actualPollutantList = $pollutantList->getPollutants();
        $expectedPollutantList = [
            'co' => new CO(),
            'no2' => new NO2(),
        ];

        $this->assertEquals($expectedPollutantList, $actualPollutantList);
    }

    public function testListWithIds(): void
    {
        $pollutantList = new PollutantList();

        $pollutantList
            ->addPollutant(new O3())
            ->addPollutant(new SO2());

        $actualPollutantList = $pollutantList->getPollutants();
        $expectedPollutantList = [
            'o3' => new O3(),
            'so2' => new SO2(),
        ];

        $this->assertEquals($expectedPollutantList, $actualPollutantList);
    }
}