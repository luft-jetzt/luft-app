<?php declare(strict_types=1);

namespace App\Producer\Value;

use App\Pollution\Value\Value;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

class RabbitValueProducer implements ValueProducerInterface
{
    /** @var ProducerInterface $producer */
    protected $producer;

    public function __construct(ProducerInterface $producer)
    {
        $this->producer = $producer;
    }

    public function publish(Value $value): ValueProducerInterface
    {
        $this->producer->publish($value);

        return $this;
    }
}