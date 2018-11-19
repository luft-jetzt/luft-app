<?php declare(strict_types=1);

namespace App\Provider\EuropeanEnvironmentAgency;

use App\Provider\AbstractProvider;

class EuropeanEnvironmentAgencyProvider extends AbstractProvider
{
    const IDENTIFIER = 'eea';

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }
}
