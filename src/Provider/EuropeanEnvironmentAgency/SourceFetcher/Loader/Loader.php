<?php declare(strict_types=1);

namespace App\Provider\EuropeanEnvironmentAgency\SourceFetcher\Loader;

use App\Pollution\Pollutant\PollutantInterface;

class Loader extends AbstractLoader
{
    public function query(PollutantInterface $pollutant, string $countryCode): string
    {
        $csvUrl = sprintf(self::BASE_URL, $countryCode, strtolower($pollutant->getIdentifier()));

        $this->curl->get($csvUrl);

        return $this->curl->response;
    }
}
