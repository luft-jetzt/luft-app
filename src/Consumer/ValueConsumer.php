<?php declare(strict_types=1);

namespace App\Consumer;

use App\Pollution\DataPersister\UniquePersisterInterface;
use OldSound\RabbitMqBundle\RabbitMq\BatchConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class ValueConsumer implements ConsumerInterface
{
    /** @var UniquePersisterInterface  */
    protected $persister;

    public function __construct(UniquePersisterInterface $persister)
    {
        $this->persister = $persister;
    }

    public function execute(AMQPMessage $message): int
    {
        $value = unserialize($message->getBody());
        
        $this->persister->persistValues([$value]);

        return self::MSG_ACK;
    }
}
