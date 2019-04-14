<?php declare(strict_types=1);

namespace App\Consumer;

use App\Pollution\DataPersister\PersisterInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class ValueConsumer implements ConsumerInterface
{
    /** @var PersisterInterface $persister */
    protected $persister;

    public function __construct(PersisterInterface $persister)
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
