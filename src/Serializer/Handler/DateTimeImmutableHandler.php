<?php declare(strict_types=1);

namespace App\Serializer\Handler;

use JMS\Serializer\Handler\SubscribingHandlerInterface;

class DateTimeImmutableHandler implements SubscribingHandlerInterface
{
    private string $format;

    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => \JMS\Serializer\GraphNavigator::DIRECTION_SERIALIZATION,
                'format'    => 'json',
                'type'      => 'DateTimeImmutable',
                'method'    => 'serializeDateTimeImmutableToJson',
            ]
        ];
    }

    public function __construct(string $format = DATE_ISO8601)
    {
        $this->format = $format;
    }

    public function serializeDateTimeImmutableToJson(
        \JMS\Serializer\JsonSerializationVisitor $visitor,
        \DateTimeImmutable $date,
        array $type,
        \JMS\Serializer\Context $context
    ) {
        return $visitor->visitString($date->format($this->format), $type, $context);
    }
}
