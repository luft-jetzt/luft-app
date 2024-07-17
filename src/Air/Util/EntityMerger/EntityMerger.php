<?php declare(strict_types=1);

namespace App\Air\Util\EntityMerger;

use Symfony\Component\Serializer\Attribute\Ignore;

class EntityMerger implements EntityMergerInterface
{
    #[\Override]
    public function merge(object $source, object $destination): object
    {
        $reflectionClass = new \ReflectionClass($source);

        /** @var \ReflectionClass $reflectionClass */
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if ($this->isPropertyExposed($reflectionProperty)) {
                $setMethodName = $this->generateSetMethodName($reflectionProperty);
                $getMethodName = $this->generateGetMethodName($reflectionProperty, $reflectionClass);

                try {
                    $newValue = $source->$getMethodName();

                    if ($newValue) {
                        $destination->$setMethodName($newValue);
                    }
                } catch (\TypeError) {
                    // deserialized entities passed to this entity merger may not be fully stuffed with properties as
                    // the serializer does not call the entity's constructor as described here:
                    // https://stackoverflow.com/questions/31948118/jms-serializer-why-are-new-objects-not-being-instantiated-through-constructor
                    //
                    // to avoid these problems, we just skipped empty or null properties and act like we just don't care.
                }
            }
        }

        return $destination;
    }

    protected function isPropertyExposed(\ReflectionProperty $reflectionProperty): bool
    {
        $propertyAttributes = $reflectionProperty->getAttributes(Ignore::class);

        foreach ($propertyAttributes as $propertyAttribute) {
            if ($propertyAttribute instanceof Ignore) {
                return false;
            }
        }

        return true;
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
