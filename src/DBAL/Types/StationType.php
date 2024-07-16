<?php declare(strict_types=1);

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class StationType extends AbstractEnumType
{
    final public const string TRAFFIC = 'traffic';
    final public const string BACKGROUND = 'background';
    final public const string INDUSTRIAL= 'industrial';

    protected static array $choices = [
        self::TRAFFIC => 'station.type.traffic',
        self::BACKGROUND => 'station.type.background',
        self::INDUSTRIAL => 'station.type.industrial',
    ];
}
