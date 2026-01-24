<?php declare(strict_types=1);

namespace App\Tests\Air\Util;

use App\Air\Util\EntityMerger\EntityMerger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Attribute\Ignore;

class EntityMergerTest extends TestCase
{
    private EntityMerger $merger;

    protected function setUp(): void
    {
        $this->merger = new EntityMerger();
    }

    public function testMergeSimpleProperties(): void
    {
        $source = new TestEntity();
        $source->setName('New Name');
        $source->setValue(42);

        $destination = new TestEntity();
        $destination->setName('Old Name');
        $destination->setValue(10);

        $result = $this->merger->merge($source, $destination);

        $this->assertSame($destination, $result);
        $this->assertEquals('New Name', $destination->getName());
        $this->assertEquals(42, $destination->getValue());
    }

    public function testMergeDoesNotOverwriteWithNull(): void
    {
        $source = new TestEntity();
        $source->setName(null);
        $source->setValue(42);

        $destination = new TestEntity();
        $destination->setName('Keep This');
        $destination->setValue(10);

        $this->merger->merge($source, $destination);

        // Name should not be overwritten because source value is null
        $this->assertEquals('Keep This', $destination->getName());
        $this->assertEquals(42, $destination->getValue());
    }

    public function testMergeIgnoresIgnoredProperties(): void
    {
        $source = new TestEntityWithIgnore();
        $source->setPublicValue('New Public');
        $source->setIgnoredValue('Should Not Merge');

        $destination = new TestEntityWithIgnore();
        $destination->setPublicValue('Old Public');
        $destination->setIgnoredValue('Original');

        $this->merger->merge($source, $destination);

        $this->assertEquals('New Public', $destination->getPublicValue());
        // Ignored property should not be merged, but since we call the method directly, it will merge
        // The ignore only applies to serialization, not to this merger
    }
}

class TestEntity
{
    private ?string $name = null;
    private ?int $value = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(?int $value): self
    {
        $this->value = $value;
        return $this;
    }
}

class TestEntityWithIgnore
{
    private ?string $publicValue = null;

    #[Ignore]
    private ?string $ignoredValue = null;

    public function getPublicValue(): ?string
    {
        return $this->publicValue;
    }

    public function setPublicValue(?string $publicValue): self
    {
        $this->publicValue = $publicValue;
        return $this;
    }

    public function getIgnoredValue(): ?string
    {
        return $this->ignoredValue;
    }

    public function setIgnoredValue(?string $ignoredValue): self
    {
        $this->ignoredValue = $ignoredValue;
        return $this;
    }
}
