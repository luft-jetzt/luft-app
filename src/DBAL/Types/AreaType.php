<?php declare(strict_types=1);

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class AreaType extends AbstractEnumType
{
    public const URBAN = 'urban';
    public const SUBURBAN = 'suburban';
    public const RURAL = 'rural';

    protected static $choices = [
        self::URBAN => 'station.area.urban',
        self::SUBURBAN => 'station.area.suburban',
        self::RURAL => 'station.area.rural',
    ];
}
