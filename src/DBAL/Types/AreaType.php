<?php declare(strict_types=1);

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class AreaType extends AbstractEnumType
{
    final public const string URBAN = 'urban';
    final public const string SUBURBAN = 'suburban';
    final public const string RURAL = 'rural';

    protected static array $choices = [
        self::URBAN => 'station.area.urban',
        self::SUBURBAN => 'station.area.suburban',
        self::RURAL => 'station.area.rural',
    ];
}
