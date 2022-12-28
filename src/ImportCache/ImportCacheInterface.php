<?php declare(strict_types=1);

namespace App\ImportCache;

interface ImportCacheInterface
{
    public const CACHE_NAMESPACE = 'luft';
    public const TTL = 172800;

    public function get(string $key): ?int;
    public function has(string $key): bool;
    public function set(string $key, int $timestamp): void;
    public function clear(): void;
}
