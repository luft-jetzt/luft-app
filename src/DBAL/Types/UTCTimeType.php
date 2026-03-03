<?php declare(strict_types=1);

namespace App\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\TimeType;

class UTCTimeType extends TimeType
{
    private static ?\DateTimeZone $utc = null;

    protected static function getUtc(): \DateTimeZone
    {
        return self::$utc ??= new \DateTimeZone('UTC');
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof \DateTime) {
            $value->setTimezone(self::getUtc());
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?\DateTime
    {
        if (null === $value || $value instanceof \DateTime) {
            return $value;
        }

        $converted = \DateTime::createFromFormat(
            '!' . $platform->getTimeFormatString(),
            $value,
            self::getUtc()
        );

        if (!$converted) {
            throw new ConversionException(sprintf(
                'Could not convert database value "%s" to DateTime with format "%s"',
                $value,
                $platform->getTimeFormatString()
            ));
        }

        return $converted;
    }
}
