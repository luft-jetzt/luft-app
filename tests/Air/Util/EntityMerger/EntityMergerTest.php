<?php declare(strict_types=1);

namespace App\Tests\Air\Util\EntityMerger;

use App\Air\Util\EntityMerger\Attribute\Mergeable;
use App\Air\Util\EntityMerger\EntityMerger;
use PHPUnit\Framework\TestCase;

class EntityMergerTest extends TestCase
{
    public function testMergeableFieldIsCopied(): void
    {
        $source = (new EntityMergerFixture())->setTitle('new title');
        $destination = (new EntityMergerFixture())->setTitle('old title');

        (new EntityMerger())->merge($source, $destination);

        $this->assertSame('new title', $destination->getTitle());
    }

    public function testNonMergeableIdentityFieldIsProtected(): void
    {
        $source = (new EntityMergerFixture())->setIdentity('attacker-value');
        $destination = (new EntityMergerFixture())->setIdentity('original-value');

        (new EntityMerger())->merge($source, $destination);

        $this->assertSame('original-value', $destination->getIdentity());
    }

    public function testFalsyZeroValueIsMerged(): void
    {
        // Regression: the old `if ($newValue)` guard discarded 0 / 0.0 / '' / false.
        $source = (new EntityMergerFixture())->setCount(0);
        $destination = (new EntityMergerFixture())->setCount(42);

        (new EntityMerger())->merge($source, $destination);

        $this->assertSame(0, $destination->getCount());
    }

    public function testNullValueIsNotMerged(): void
    {
        $source = new EntityMergerFixture(); // title stays null
        $destination = (new EntityMergerFixture())->setTitle('keep me');

        (new EntityMerger())->merge($source, $destination);

        $this->assertSame('keep me', $destination->getTitle());
    }

    public function testUninitializedTypedPropertyIsSkipped(): void
    {
        // Serializer-hydrated sources can leave typed properties uninitialized; reading the
        // getter throws a TypeError which must be swallowed instead of aborting the merge.
        $source = (new EntityMergerFixture())->setTitle('from source'); // $number left uninitialized
        $destination = (new EntityMergerFixture())->setTitle('dest')->setNumber(7);

        (new EntityMerger())->merge($source, $destination);

        $this->assertSame('from source', $destination->getTitle());
        $this->assertSame(7, $destination->getNumber());
    }

    public function testMergeReturnsDestination(): void
    {
        $destination = new EntityMergerFixture();

        $this->assertSame($destination, (new EntityMerger())->merge(new EntityMergerFixture(), $destination));
    }
}

class EntityMergerFixture
{
    #[Mergeable]
    private ?string $title = null;

    private ?string $identity = null;

    #[Mergeable]
    private ?int $count = null;

    #[Mergeable]
    private int $number;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getIdentity(): ?string
    {
        return $this->identity;
    }

    public function setIdentity(?string $identity): self
    {
        $this->identity = $identity;

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

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }
}
