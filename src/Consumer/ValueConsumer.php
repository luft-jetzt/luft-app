<?php declare(strict_types=1);

namespace App\Consumer;

use App\Pollution\DataPersister\PersisterInterface;
use App\Pollution\Value\Value;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Serializer\SerializerInterface;

class ValueConsumer implements ConsumerInterface
{
    public function __construct(protected PersisterInterface $persister, protected SerializerInterface $serializer)
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
