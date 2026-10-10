<?php declare(strict_types=1);

namespace App\Air\Util\EntityMerger;

use App\Air\Util\EntityMerger\Attribute\Mergeable;

class EntityMerger implements EntityMergerInterface
{
    #[\Override]
    public function merge(object $source, object $destination): object
    {
        $reflectionClass = new \ReflectionClass($source);

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if (!$this->isPropertyMergeable($reflectionProperty)) {
                continue;
            }

            // Deserialized entities may have uninitialized typed properties, because the serializer
            // does not call the entity constructor. Reading such a property would throw an Error,
            // so we skip anything that is not initialized on the source. See
            // https://stackoverflow.com/questions/31948118/jms-serializer-why-are-new-objects-not-being-instantiated-through-constructor
            if (!$reflectionProperty->isInitialized($source)) {
                continue;
            }

            $getMethodName = $this->generateGetMethodName($reflectionProperty, $reflectionClass);
            $setMethodName = $this->generateSetMethodName($reflectionProperty);

            if (null === $getMethodName || !$reflectionClass->hasMethod($setMethodName)) {
                continue;
            }

            $newValue = $source->$getMethodName();

            // Only null is treated as "not provided". Falsy-but-valid values such as 0, 0.0,
            // '' or false are intentionally allowed so a field can be corrected to them.
            if (null !== $newValue) {
                $destination->$setMethodName($newValue);
            }
        }

        return $destination;
    }

    /**
     * Allowlist: a property is only merged when it explicitly carries the #[Mergeable] attribute.
     * Identity keys and system fields are therefore protected by default.
     */
    protected function isPropertyMergeable(\ReflectionProperty $reflectionProperty): bool
    {
        return count($reflectionProperty->getAttributes(Mergeable::class)) > 0;
    }

    protected function generateSetMethodName(\ReflectionProperty $reflectionProperty): string
    {
        return sprintf('set%s', ucfirst($reflectionProperty->getName()));
    }

    protected function generateGetMethodName(\ReflectionProperty $reflectionProperty, \ReflectionClass $reflectionClass): ?string
    {
        $getMethodPrefixes = ['get', 'has', 'is'];

        foreach ($getMethodPrefixes as $getMethodPrefix) {
            $getMethodName = sprintf('%s%s', $getMethodPrefix, ucfirst($reflectionProperty->getName()));

            if ($reflectionClass->hasMethod($getMethodName)) {
                return $getMethodName;
            }
        }

        return null;
    }
}
