<?php declare(strict_types=1);

namespace App\Consumer;

use App\Air\DataPersister\PersisterInterface;
use App\Air\Value\Value;
use App\Serializer\LuftSerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class ValueConsumer implements ConsumerInterface
{
    public function __construct(protected PersisterInterface $persister, protected LuftSerializerInterface $serializer)
    {
    }

    #[\Override]
    public function execute(AMQPMessage $message): int
    {
        $value = $this->serializer->deserialize($message->getBody(), Value::class, 'json');

        $this->persister->persistValues([$value]);

        return self::MSG_ACK;
    }
}
