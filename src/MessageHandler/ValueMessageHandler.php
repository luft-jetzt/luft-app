<?php declare(strict_types=1);

namespace App\MessageHandler;

use App\Air\DataPersister\PersisterInterface;
use Caldera\LuftModel\Model\Value;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\Handler\BatchHandlerInterface;
use Symfony\Component\Messenger\Handler\BatchHandlerTrait;

#[AsMessageHandler]
final class ValueMessageHandler implements BatchHandlerInterface
{
    use BatchHandlerTrait;

    private const int BATCH_SIZE = 100;

    public function __construct(private PersisterInterface $persister)
    {

    }

    public function __invoke(Value $value, ?Acknowledger $ack = null): mixed
    {
        return $this->handle($value, $ack);
    }

    private function process(array $messageList): void
    {
        $valueList = [];

        foreach ($messageList as [$value, $ack]) {
            $valueList[] = $value;

            $ack->ack($value);
        }

        $this->persister->persistValues($valueList);
    }

    private function shouldFlush(): bool
    {
        return $this->getBatchSize() <= \count($this->jobs);
    }

    // ... or redefine the `getBatchSize()` method if the default
    // flush behavior suits your needs
    private function getBatchSize(): int
    {
        return self::BATCH_SIZE;
    }
}
