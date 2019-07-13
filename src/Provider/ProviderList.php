<?php declare(strict_types=1);

namespace App\Provider;

class ProviderList implements ProviderListInterface
{
    protected $list = [];

    public function addProvider(ProviderInterface $provider): ProviderListInterface
    {
        $this->list[$provider->getIdentifier()] = $provider;

        return $this;
    }

    public function getProvider(string $identifier): ?ProviderInterface
    {
        if (array_key_exists($identifier, $this->list)) {
            return $this->list[$identifier];
        }

        return null;
    }

    public function getIdentifierList(): array
    {
        return array_keys($this->list);
    }

    public function getList(): array
    {
        return $this->list;
    }
}
