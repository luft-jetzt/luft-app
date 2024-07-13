<?php declare(strict_types=1);

namespace App\Tests\Air\AirQuality\Calculator;

use App\Air\AirQuality\Calculator\AirQualityCalculator;
use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;
use App\Air\Pollutant\CO;
use App\Air\ViewModel\PollutantViewModel;
use App\Entity\Data;
use PHPUnit\Framework\TestCase;

class AirQualityCalculatorTest extends TestCase
{
    public function testLevel(): void
    {
        $data = (new Data())->setValue(50);
        $viewModel = $this->viewModel($data);

        $airQualityCalculator = new AirQualityCalculator();

        $airQualityCalculator->addPollutionLevel($this->pollutionLevel())->calculateViewModel($viewModel);

        $this->assertEquals(1, $viewModel->getPollutionLevel());
    }

    public function testAnotherLevel(): void
    {
        $data = (new Data())->setValue(150);
        $viewModel = $this->viewModel($data);

        $airQualityCalculator = new AirQualityCalculator();

        $airQualityCalculator->addPollutionLevel($this->pollutionLevel())->calculateViewModel($viewModel);

        $this->assertEquals(2, $viewModel->getPollutionLevel());
    }

    protected function viewModel(Data $data): PollutantViewModel
    {
        $measurementViewModel = new PollutantViewModel($data);
        $measurementViewModel->setPollutant(new CO());

        return $measurementViewModel;
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