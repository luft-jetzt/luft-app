<?php declare(strict_types=1);

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class StationType extends AbstractEnumType
{
    public const TRAFFIC = 'traffic';
    public const BACKGROUND = 'background';
    public const INDUSTRIAL= 'industrial';

    protected static $choices = [
        self::TRAFFIC => 'station.type.traffic',
        self::BACKGROUND => 'station.type.background',
        self::INDUSTRIAL => 'station.type.industrial',
    ];
}
