<?php declare(strict_types=1);

namespace App\Producer\Value;

use App\Pollution\Value\Value;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

class RabbitValueProducer implements ValueProducerInterface
{
    protected ProducerInterface $producer;

    public function __construct(ProducerInterface $producer)
    {
        $this->producer = $producer;
    }

    /** @deprecated */
    public function publish(Value $value): ValueProducerInterface
    {
        $this->producer->publish($value);

        return $this;
    }

    public function publishValue(Value $value): ValueProducerInterface
    {
        $this->producer->publish($value);

        return $this;
    }

    public function publishValues(array $valueList): ValueProducerInterface
    {
        /** @var Value $value */
        foreach ($valueList as $value) {
            $this->producer->publish($value);
        }

        return $this;
    }
}