<?php declare(strict_types=1);

namespace App\Util;

class DateTimeUtil
{
    public static function getYearStartDateTime(\DateTime $year): \DateTime
    {
        $dateTime = sprintf('%d-01-01 00:00:00', $year->format('Y'));

        return new \DateTime($dateTime);
    }

    public static function getYearEndDateTime(\DateTime $year): \DateTime
    {
        $dateTime = sprintf('%d-12-31 23:59:59', $year->format('Y'));

        return new \DateTime($dateTime);
    }

    public static function getMonthStartDateTime(\DateTime $month): \DateTime
    {
        $dateTime = sprintf('%d-%d-01 00:00:00', $month->format('Y'), $month->format('m'));

        return new \DateTime($dateTime);
    }

    public static function getMonthEndDateTime(\DateTime $month): \DateTime
    {
        $dateTime = sprintf('%d-%d-%d 23:59:59', $month->format('Y'), $month->format('m'), $month->format('t'));

        return new \DateTime($dateTime);
    }

    public static function getDayStartDateTime(\DateTime $day): \DateTime
    {
        $dateTime = sprintf('%d-%d-%d 00:00:00', $day->format('Y'), $day->format('m'), $day->format('d'));

        return new \DateTime($dateTime);
    }

    public static function getDayEndDateTime(\DateTime $day): \DateTime
    {
        $dateTime = sprintf('%d-%d-%d 23:59:59', $day->format('Y'), $day->format('m'), $day->format('d'));

        return new \DateTime($dateTime);
    }

    public static function getHourStartDateTime(\DateTime $hour): \DateTime
    {
        $dateTime = sprintf('%d-%d-%d %d:00:00', $hour->format('Y'), $hour->format('m'), $hour->format('d'), $hour->format('H'));

        return new \DateTime($dateTime);
    }

    public static function getHourEndDateTime(\DateTime $hour): \DateTime
    {
        $dateTime = sprintf('%d-%d-%d %d:59:59', $hour->format('Y'), $hour->format('m'), $hour->format('d'), $hour->format('H'));

        return new \DateTime($dateTime);
    }

    public static function getMinuteStartDateTime(\DateTime $minute): \DateTime
    {
        $dateTime = sprintf('%d-%d-%d %d:%d:00', $minute->format('Y'), $minute->format('m'), $minute->format('d'), $minute->format('H'), $minute->format('m'));

        return new \DateTime($dateTime);
    }

    public static function getMinuteEndDateTime(\DateTime $minute): \DateTime
    {
        $dateTime = sprintf('%d-%d-%d %d:%d:59', $minute->format('Y'), $minute->format('m'), $minute->format('d'), $minute->format('H'), $minute->format('m'));

        return new \DateTime($dateTime);
    }
}
