<?php declare(strict_types=1);

namespace App\Tests\Air\AirQuality\LevelCalculator;

use App\Air\AirQuality\LevelCalculator\LevelCalculator;
use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;
use App\Entity\Data;
use PHPUnit\Framework\TestCase;

class LevelCalculatorTest extends TestCase
{
    public function testLevelCalculationWithPlusOne(): void
    {
        $this->assertEquals(1, LevelCalculator::calculate($this->createPollutionLevel(), (new Data())->setValue(5)));
        $this->assertEquals(2, LevelCalculator::calculate($this->createPollutionLevel(), (new Data())->setValue(15)));
        $this->assertEquals(3, LevelCalculator::calculate($this->createPollutionLevel(), (new Data())->setValue(25)));
    }

    public function testLevelCalculationWithoutPlusOne(): void
    {
        $this->assertEquals(0, LevelCalculator::calculate($this->createPollutionLevel(), (new Data())->setValue(5), false));
        $this->assertEquals(1, LevelCalculator::calculate($this->createPollutionLevel(), (new Data())->setValue(15), false));
        $this->assertEquals(2, LevelCalculator::calculate($this->createPollutionLevel(), (new Data())->setValue(25), false));
    }

    public function testLevelCalculationBordersWithPlusOne(): void
    {
        $this->assertEquals(1, LevelCalculator::calculate($this->createPollutionLevel(), (new Data())->setValue(9)));
        $this->assertEquals(2, LevelCalculator::calculate($this->createPollutionLevel(), (new Data())->setValue(10)));
        $this->assertEquals(2, LevelCalculator::calculate($this->createPollutionLevel(), (new Data())->setValue(11)));
    }

    public function testLevelCalculationBordersWithoutPlusOne(): void
    {
        $this->assertEquals(0, LevelCalculator::calculate($this->createPollutionLevel(), (new Data())->setValue(9), false));
        $this->assertEquals(1, LevelCalculator::calculate($this->createPollutionLevel(), (new Data())->setValue(10), false));
        $this->assertEquals(1, LevelCalculator::calculate($this->createPollutionLevel(), (new Data())->setValue(11), false));
    }

    public function testLevelCalculationWithNegativeDataValue(): void
    {
        $this->assertEquals(1, LevelCalculator::calculate($this->createPollutionLevel(), (new Data())->setValue(-5)));
        $this->assertEquals(0, LevelCalculator::calculate($this->createPollutionLevel(), (new Data())->setValue(-5), false));
    }

    protected function createPollutionLevel(): PollutionLevelInterface
    {
        return new Class implements PollutionLevelInterface
        {
            public function getLevel(Data $data): int
            {
                return 0;
            }

            public function getLevels(): array
            {
                return [
                    0 => 10,
                    1 => 20,
                    2 => 30,
                ];
            }

            public function getPollutionIdentifier(): string
            {
                return '';
            }
        };
    }
}