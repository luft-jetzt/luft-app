<?php declare(strict_types=1);

namespace App\Producer\Value;

use App\Pollution\DataPersister\CacheOrmPersister;
use App\Pollution\DataPersister\CachePersister;
use App\Pollution\Value\Value;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

class CacheRabbitValueProducer implements ValueProducerInterface
{
    protected CachePersister $persister;
    protected ProducerInterface $producer;

    public function __construct(CachePersister $persister, ProducerInterface $producer)
    {
        $this->persister = $persister;
        $this->producer = $producer;
    }

    /** @deprecated  */
    public function publish(Value $value): ValueProducerInterface
    {
        $this->persister->persistValues([$value]);
        $this->producer->publish($value);
        
        return $this;
    }

    public function publishValue(Value $value): ValueProducerInterface
    {
        $this->persister->persistValues([$value]);
        $this->producer->publish($value);

        return $this;
    }

    public function publishValues(array $valueList): ValueProducerInterface
    {
        $this->persister->persistValues($valueList);

        /** @var Value $value */
        foreach ($valueList as $value) {
            $this->producer->publish($value);
        }

        return $this;
    }
}
