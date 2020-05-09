<?php declare(strict_types=1);

namespace App\Provider\OpenWeatherMapProvider;

use App\Air\Measurement\CO;
use App\Air\Measurement\NO2;
use App\Air\Measurement\O3;
use App\Air\Measurement\PM10;
use App\Air\Measurement\SO2;
use App\Air\Measurement\Temperature;
use App\Air\Measurement\UVIndex;
use App\Provider\AbstractProvider;

class OpenWeatherMapProvider extends AbstractProvider
{
    const IDENTIFIER = 'owm';

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    public function providedMeasurements(): array
    {
        return [
            Temperature::class,
            UVIndex::class,
        ];
    }
}
