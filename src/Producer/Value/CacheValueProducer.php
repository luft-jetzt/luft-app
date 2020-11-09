<?php declare(strict_types=1);

namespace App\Producer\Value;

use App\Pollution\DataPersister\CachePersister;
use App\Pollution\Value\Value;

class CacheValueProducer implements ValueProducerInterface
{
    protected CachePersister $persister;

    public function __construct(CachePersister $persister)
    {
        $this->persister = $persister;
    }

    /** @deprecated  */
    public function publish(Value $value): ValueProducerInterface
    {
        $this->persister->persistValues([$value]);
        
        return $this;
    }

    public function publishValue(Value $value): ValueProducerInterface
    {
        $this->persister->persistValues([$value]);

        return $this;
    }

    public function publishValues(array $valueList): ValueProducerInterface
    {
        $this->persister->persistValues($valueList);

        return $this;
    }
}
