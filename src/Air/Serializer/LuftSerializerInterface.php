<?php declare(strict_types=1);

namespace App\Air\Serializer;

interface LuftSerializerInterface
{
    final public const string FORMAT = 'json';

    public function serialize(mixed $data, string $format = self::FORMAT, array $context = []): string;
    public function deserialize(mixed $data, string $type, string $format = self::FORMAT, array $context = []): mixed;
}
