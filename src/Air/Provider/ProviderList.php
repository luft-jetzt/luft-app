<?php declare(strict_types=1);

namespace App\Air\Provider;

class ProviderList implements ProviderListInterface
{
    protected $list = [];

    #[\Override]
    public function addProvider(ProviderInterface $provider): ProviderListInterface
    {
        $this->list[$provider->getIdentifier()] = $provider;

        return $this;
    }

    #[\Override]
    public function getProvider(string $identifier): ?ProviderInterface
    {
        if (array_key_exists($identifier, $this->list)) {
            return $this->list[$identifier];
        }

        return null;
    }

    #[\Override]
    public function getIdentifierList(): array
    {
        return array_keys($this->list);
    }

    #[\Override]
    public function getList(): array
    {
        return $this->list;
    }
}
