<?php declare(strict_types=1);

namespace App\Producer\Value;

use App\Pollution\DataPersister\CachePersister;
use App\Pollution\Value\Value;

class CacheValueProducer implements ValueProducerInterface
{
    /** @var CachePersister $persister */
    protected $persister;

    public function __construct(CachePersister $persister)
    {
        $this->persister = $persister;
    }

    public function publish(Value $value): ValueProducerInterface
    {
        $this->persister->persistValues([$value]);
        
        return $this;
    }
}