<?php declare(strict_types=1);

 namespace App\Serializer\Handler;

 use JMS\Serializer\Handler\SubscribingHandlerInterface;

 class DateTimeImmutableHandler implements SubscribingHandlerInterface
 {
     #[\Override]
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

     public function __construct(private readonly string $format = DATE_ISO8601)
     {
     }

     public function serializeDateTimeImmutableToJson(
         \JMS\Serializer\JsonSerializationVisitor $visitor,
         \DateTimeImmutable $date,
         array $type,
         \JMS\Serializer\Context $context
     ) {
         return $visitor->visitString($date->format($this->format), $type);
     }
 }
