<?php declare(strict_types=1);

namespace App\Provider;

interface ProviderListInterface
{
    public function addProvider(ProviderInterface $provider): ProviderListInterface;
    public function getProvider(string $identifier): ?ProviderInterface;
    public function getIdentifierList(): array;
    public function getList(): array;
}
