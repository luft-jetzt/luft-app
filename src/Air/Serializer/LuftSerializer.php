<?php declare(strict_types=1);

namespace App\Air\Serializer;

use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class LuftSerializer implements LuftSerializerInterface
{
    private SerializerInterface $serializer;

    public function __construct()
    {
        $this->createSerializer();
    }

    public function serialize(mixed $data, string $format = self::FORMAT, array $context = []): string
    {
        $context[AbstractObjectNormalizer::SKIP_NULL_VALUES] = true;

        return $this->serializer->serialize($data, $format, $context);
    }

    public function deserialize(mixed $data, string $type, string $format = self::FORMAT, array $context = []): mixed
    {
        return $this->serializer->deserialize($data, $type, $format, $context);
    }

    private function createSerializer(): void
    {
        $dateTimeNormalizerOptions = [
            DateTimeNormalizer::FORMAT_KEY => 'U',
        ];

        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());

        $normalizers = [
            new DateTimeNormalizer($dateTimeNormalizerOptions),
            new ObjectNormalizer(
                classMetadataFactory: $classMetadataFactory,
                nameConverter: new CamelCaseToSnakeCaseNameConverter(),
                propertyTypeExtractor: new ReflectionExtractor(),
            ),
            new GetSetMethodNormalizer(
                classMetadataFactory: $classMetadataFactory,
                nameConverter: new CamelCaseToSnakeCaseNameConverter(),
            ),
            new ArrayDenormalizer(),
        ];

        $encoders = [
            new JsonEncoder()
        ];

        $this->serializer = new Serializer($normalizers, $encoders);
    }
}
