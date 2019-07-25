<?php declare(strict_types=1);

namespace App\Provider\OpenWeatherMapProvider;

use App\Provider\AbstractProvider;

class OpenWeatherMapProvider extends AbstractProvider
{
    const IDENTIFIER = 'owm';

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }
}
