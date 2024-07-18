<?php declare(strict_types=1);

namespace App\MessageHandler;

use App\Air\DataPersister\PersisterInterface;
use Caldera\LuftModel\Model\Value;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ValueMessageHandler
{
    public function __construct(private PersisterInterface $persister)
    {

    }

    public function __invoke(Value $value): void
    {
        $this->persister->persistValues([$value]);
    }
}
