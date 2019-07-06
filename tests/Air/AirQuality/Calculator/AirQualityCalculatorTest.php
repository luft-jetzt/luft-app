<?php declare(strict_types=1);

namespace App\Tests\Air\AirQuality\Calculator;

use App\Air\AirQuality\Calculator\AirQualityCalculator;
use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;
use App\Air\Measurement\CO;
use App\Entity\Data;
use App\Pollution\Box\Box;
use PHPUnit\Framework\TestCase;

class AirQualityCalculatorTest extends TestCase
{
    public function testLevel(): void
    {
        $data = (new Data())->setValue(50);
        $box = $this->box($data);

        $airQualityCalculator = new AirQualityCalculator();

        $airQualityCalculator->addPollutionLevel($this->pollutionLevel())->calculateBox($box);

        $this->assertEquals(1, $box->getPollutionLevel());
    }

    public function testAnotherLevel(): void
    {
        $data = (new Data())->setValue(150);
        $box = $this->box($data);

        $airQualityCalculator = new AirQualityCalculator();

        $airQualityCalculator->addPollutionLevel($this->pollutionLevel())->calculateBox($box);

        $this->assertEquals(2, $box->getPollutionLevel());
    }

    protected function box(Data $data): Box
    {
        $box = new Box($data);
        $box->setMeasurement(new CO());

        return $box;
    }

    protected function pollutionLevel(): PollutionLevelInterface
    {
        return new Class implements PollutionLevelInterface
        {
            public function getLevels(): array
            {
                return [
                    100,
                    200,
                    300,
                    400,
                ];
            }

            public function getPollutionIdentifier(): string
            {
                return 'co';
            }
        };
    }
}