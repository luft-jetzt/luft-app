<?php declare(strict_types=1);

namespace App\Consumer;

use App\Pollution\DataPersister\PersisterInterface;
use App\Pollution\Value\Value;
use JMS\Serializer\SerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class ValueConsumer implements ConsumerInterface
{
    protected PersisterInterface $persister;
    protected SerializerInterface $serializer;

    public function __construct(PersisterInterface $persister, SerializerInterface $serializer)
    {
        $this->persister = $persister;
        $this->serializer = $serializer;
    }

    public function execute(AMQPMessage $message): int
    {
        $value = $this->serializer->deserialize($message->getBody(), Value::class, 'json');

        $this->persister->persistValues([$value]);

        return self::MSG_ACK;
    }
}
