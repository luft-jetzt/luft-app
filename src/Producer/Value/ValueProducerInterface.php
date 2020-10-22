<?php declare(strict_types=1);

namespace App\Producer\Value;

use App\Pollution\Value\Value;

interface ValueProducerInterface
{
    public function publish(Value $value): ValueProducerInterface;
}