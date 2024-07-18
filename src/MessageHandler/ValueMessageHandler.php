<?php declare(strict_types=1);

namespace App\MessageHandler;

use App\Air\Value\Value;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ValueMessageHandler
{
    public function __invoke(Value $value): void
    {
        echo 'lalalala';
    }
}
