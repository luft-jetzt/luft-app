<?php declare(strict_types=1);

namespace App\Pollution\DataPersister;

class CacheOrmPersister implements PersisterInterface
{
    protected CachePersister $cachePersister;
    protected OrmPersister $ormPersister;

    public function __construct(CachePersister $cachePersister, OrmPersister $ormPersister)
    {
        $this->cachePersister = $cachePersister;
        $this->ormPersister = $ormPersister;
    }

    public function persistValues(array $values): PersisterInterface
    {
        $this->cachePersister->persistValues($values);
        $this->ormPersister->persistValues($values);

        return $this;
    }

    public function getNewValueList(): array
    {
        return array_merge($this->cachePersister->getNewValueList(), $this->ormPersister->getNewValueList());
    }

    public function reset(): PersisterInterface
    {
        $this->cachePersister->reset();
        $this->ormPersister->reset();

        return $this;
    }
}
