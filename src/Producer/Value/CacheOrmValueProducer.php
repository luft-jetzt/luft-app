<?php declare(strict_types=1);

namespace App\Producer\Value;

use App\Pollution\DataPersister\CacheOrmPersister;
use App\Pollution\DataPersister\CachePersister;
use App\Pollution\Value\Value;

class CacheOrmValueProducer implements ValueProducerInterface
{
    protected $persister;

    public function __construct(CacheOrmPersister $persister)
    {
        $this->persister = $persister;
    }

    public function publish(Value $value): ValueProducerInterface
    {
        $this->persister->persistValues([$value]);
        
        return $this;
    }
}