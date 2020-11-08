<?php declare(strict_types=1);

namespace App\Producer\Value;

use App\Pollution\Value\Value;

interface ValueProducerInterface
{
    /** @deprecated  */
    public function publish(Value $value): ValueProducerInterface;
    public function publishValue(Value $value): ValueProducerInterface;
    public function publishValues(array $valueList): ValueProducerInterface;
}