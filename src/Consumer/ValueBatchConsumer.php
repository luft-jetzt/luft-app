<?php declare(strict_types=1);

namespace App\Consumer;

use App\Air\DataPersister\PersisterInterface;
use App\Air\Value\Value;
use App\Air\Serializer\LuftSerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\BatchConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class ValueBatchConsumer implements BatchConsumerInterface
{
    public function __construct(protected PersisterInterface $persister, protected LuftSerializerInterface $serializer)
    {
    }

    #[\Override]
    public function batchExecute(array $messages): array
    {
        $valueList = [];
        $resultList = [];

        /** @var AMQPMessage $message */
        foreach ($messages as $message) {
            $valueList[] = $this->serializer->deserialize($message->getBody(), Value::class, 'json');

            $resultList[(int)$message->delivery_info['delivery_tag']] = true;
        }

        $this->persister->persistValues($valueList);

        return $resultList;
    }
}
