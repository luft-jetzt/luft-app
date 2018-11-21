<?php declare(strict_types=1);

namespace App\Consumer;

use App\Pollution\DataPersister\UniquePersisterInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ValueConsumer implements ConsumerInterface
{
    /** @var LoggerInterface $logger */
    protected $logger;

    /** @var UniquePersisterInterface  */
    protected $persister;

    public function __construct(RegistryInterface $registry, UniquePersisterInterface $persister, LoggerInterface $logger)
    {
        $this->logger = $logger;

        $this->persister = $persister;
    }

    public function execute(AMQPMessage $msg): bool
    {
        $this->persister->persistValues([$msg->getBody()]);

        $this->logger->log('FOO ', serialize($msg->getBody()));

        return false;
    }
}
