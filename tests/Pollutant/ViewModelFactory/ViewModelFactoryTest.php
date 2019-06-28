<?php declare(strict_types=1);

namespace App\Tests\Pollutant\ViewModelFactory;

use App\AirQuality\Calculator\AirQualityCalculator;
use App\Pollution\ViewModelFactory\ViewModelFactory;
use PHPUnit\Framework\TestCase;

class ViewModelFactoryTest extends TestCase
{
    public function test(): void
    {
        $airQualityCalculator = new AirQualityCalculator();
        $viewModelFactory = new ViewModelFactory($airQualityCalculator);

        $list = $viewModelFactory->decorate()->getPollutantList();

        $this->assertEquals([], $list);
    }
}