<?php declare(strict_types=1);

namespace App\Provider\HqcasanovaProvider;

use App\Provider\AbstractProvider;

class HqcasanovaProvider extends AbstractProvider
{
    const IDENTIFIER = 'hqc';

    public function __construct()
    {
    }

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }
}
