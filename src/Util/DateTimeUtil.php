<?php declare(strict_types=1);

namespace App\Util;

class DateTimeUtil
{
    protected static function createDateTimeObject(\DateTimeInterface $dateTime, string $dateTimeSpec): \DateTimeInterface
    {
        $fqcn = get_class($dateTime);

        return new $fqcn($dateTimeSpec);
    }

    public static function getYearStartDateTime(\DateTimeInterface $year): \DateTimeInterface
    {
        $dateTimeSpec = sprintf('%d-01-01 00:00:00', $year->format('Y'));

        return self::createDateTimeObject($year, $dateTimeSpec);
    }

    public static function getYearEndDateTime(\DateTimeInterface $year): \DateTimeInterface
    {
        $dateTimeSpec = sprintf('%d-12-31 23:59:59', $year->format('Y'));

        return self::createDateTimeObject($year, $dateTimeSpec);
    }

    public static function getMonthStartDateTime(\DateTimeInterface $month): \DateTimeInterface
    {
        $dateTimeSpec = sprintf('%d-%d-01 00:00:00', $month->format('Y'), $month->format('m'));

        return self::createDateTimeObject($month, $dateTimeSpec);
    }

    public static function getMonthEndDateTime(\DateTimeInterface $month): \DateTimeInterface
    {
        $dateTimeSpec = sprintf('%d-%d-%d 23:59:59', $month->format('Y'), $month->format('m'), $month->format('t'));

        return self::createDateTimeObject($month, $dateTimeSpec);
    }

    public static function getDayStartDateTime(\DateTimeInterface $day): \DateTimeInterface
    {
        $dateTimeSpec = sprintf('%d-%d-%d 00:00:00', $day->format('Y'), $day->format('m'), $day->format('d'));

        return self::createDateTimeObject($day, $dateTimeSpec);
    }

    public static function getDayEndDateTime(\DateTimeInterface $day): \DateTimeInterface
    {
        $dateTimeSpec = sprintf('%d-%d-%d 23:59:59', $day->format('Y'), $day->format('m'), $day->format('d'));

        return self::createDateTimeObject($day, $dateTimeSpec);
    }

    public static function getHourStartDateTime(\DateTimeInterface $hour): \DateTimeInterface
    {
        $dateTimeSpec = sprintf('%d-%d-%d %d:00:00', $hour->format('Y'), $hour->format('m'), $hour->format('d'), $hour->format('H'));

        return self::createDateTimeObject($hour, $dateTimeSpec);
    }

    public static function getHourEndDateTime(\DateTimeInterface $hour): \DateTimeInterface
    {
        $dateTimeSpec = sprintf('%d-%d-%d %d:59:59', $hour->format('Y'), $hour->format('m'), $hour->format('d'), $hour->format('H'));

        return self::createDateTimeObject($hour, $dateTimeSpec);
    }

    public static function getMinuteStartDateTime(\DateTimeInterface $minute): \DateTimeInterface
    {
        $dateTimeSpec = sprintf('%d-%d-%d %d:%d:00', $minute->format('Y'), $minute->format('m'), $minute->format('d'), $minute->format('H'), $minute->format('m'));

        return self::createDateTimeObject($minute, $dateTimeSpec);
    }

    public static function getMinuteEndDateTime(\DateTimeInterface $minute): \DateTimeInterface
    {
        $dateTimeSpec = sprintf('%d-%d-%d %d:%d:59', $minute->format('Y'), $minute->format('m'), $minute->format('d'), $minute->format('H'), $minute->format('m'));

        return self::createDateTimeObject($minute, $dateTimeSpec);
    }
}
