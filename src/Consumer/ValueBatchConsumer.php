<?php declare(strict_types=1);

namespace App\Consumer;

use App\Pollution\DataPersister\UniquePersisterInterface;
use OldSound\RabbitMqBundle\RabbitMq\BatchConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class ValueBatchConsumer implements BatchConsumerInterface
{
    /** @var UniquePersisterInterface  */
    protected $persister;

    public function __construct(UniquePersisterInterface $persister)
    {
        $this->persister = $persister;
    }

    public function batchExecute(array $messages): array
    {
        $valueList = [];
        $resultList = [];

        /** @var AMQPMessage $message */
        foreach ($messages as $message) {
            $valueList[] = unserialize($message->getBody());

            $resultList[(int)$message->delivery_info['delivery_tag']] = true;
        }

        $this->persister->persistValues($valueList);

        return $resultList;
    }
}
