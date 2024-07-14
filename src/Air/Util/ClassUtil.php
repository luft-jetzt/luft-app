<?php declare(strict_types=1);

namespace App\Air\Util;

/**
 * @deprecated Use Symfony/String instead
 */
class ClassUtil
{
    public static function getShortname($object): string
    {
        $reflectionClass = new \ReflectionClass($object);

        return $reflectionClass->getShortName();
    }

    public static function getLowercaseShortname($object): string
    {
        return strtolower(self::getShortname($object));
    }

    public static function getUnderscoreShortname($object): string
    {
        return strtolower(self::getShortname($object));
    }
}
