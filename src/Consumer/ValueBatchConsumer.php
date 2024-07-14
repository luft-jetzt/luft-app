<?php declare(strict_types=1);

namespace App\Consumer;

use App\Pollution\DataPersister\PersisterInterface;
use App\Pollution\Value\Value;
use OldSound\RabbitMqBundle\RabbitMq\BatchConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Serializer\SerializerInterface;

class ValueBatchConsumer implements BatchConsumerInterface
{
    public function __construct(protected PersisterInterface $persister, protected SerializerInterface $serializer)
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
