<?php declare(strict_types=1);

namespace App\Consumer;

use App\Pollution\DataPersister\UniquePersisterInterface;
use App\Provider\ProviderInterface;
use App\Provider\UmweltbundesamtDe\UmweltbundesamtDeProvider;
use OldSound\RabbitMqBundle\RabbitMq\BatchConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class ValueConsumer implements BatchConsumerInterface
{
    /** @var UniquePersisterInterface  */
    protected $persister;

    /** @var ProviderInterface $provider */
    protected $provider;

    public function __construct(UniquePersisterInterface $persister, UmweltbundesamtDeProvider $provider)
    {
        $this->persister = $persister;
        $this->provider = $provider;
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

        $this->persister->setProvider($this->provider)->persistValues($valueList);

        return $resultList;
    }
}
