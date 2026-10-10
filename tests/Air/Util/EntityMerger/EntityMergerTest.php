<?php declare(strict_types=1);

namespace App\Tests\Air\Util\EntityMerger;

use App\Air\Util\EntityMerger\EntityMerger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Attribute\Ignore;

class EntityMergerTest extends TestCase
{
    private EntityMerger $entityMerger;

    protected function setUp(): void
    {
        $this->entityMerger = new EntityMerger();
    }

    public function testNonNullValuesAreCopied(): void
    {
        $source = (new EntityMergerFixture())->setName('new')->setCount(42);
        $destination = (new EntityMergerFixture())->setName('old')->setCount(1);

        $this->entityMerger->merge($source, $destination);

        $this->assertSame('new', $destination->getName());
        $this->assertSame(42, $destination->getCount());
    }

    public function testNullValuesAreNotCopied(): void
    {
        $source = (new EntityMergerFixture())->setName(null);
        $destination = (new EntityMergerFixture())->setName('keep');

        $this->entityMerger->merge($source, $destination);

        $this->assertSame('keep', $destination->getName());
    }

    public function testFalsyButNonNullValuesAreCopied(): void
    {
        // Regression: vorher verwarf `if ($newValue)` die Werte 0 / '' / false.
        $source = (new EntityMergerFixture())->setCount(0)->setName('');
        $destination = (new EntityMergerFixture())->setCount(99)->setName('old');

        $this->entityMerger->merge($source, $destination);

        $this->assertSame(0, $destination->getCount());
        $this->assertSame('', $destination->getName());
    }

    public function testIgnoredPropertiesAreNotCopied(): void
    {
        // Regression: der frühere instanceof-Check war immer false, #[Ignore] also wirkungslos.
        $source = (new EntityMergerFixture())->setSecret('injected');
        $destination = (new EntityMergerFixture())->setSecret('protected');

        $this->entityMerger->merge($source, $destination);

        $this->assertSame('protected', $destination->getSecret());
    }
}

class EntityMergerFixture
{
    private ?string $name = null;

    private ?int $count = null;

    #[Ignore]
    private ?string $secret = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): self
    {
        $this->count = $count;

        return $this;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function setSecret(?string $secret): self
    {
        $this->secret = $secret;

        return $this;
    }
}
