<?php

declare(strict_types=1);

namespace Hyra\AbnLookup;

use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\Extractor\SerializerExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class Dependencies
{
    public static function serializer(): Serializer
    {
        $classMetadataFactory       = new ClassMetadataFactory(new AttributeLoader());
        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);
        $propertyAccessor           = new PropertyAccessor();
        /** @var iterable<PropertyTypeExtractorInterface> $typeExtractors */
        $typeExtractors = [new PhpDocExtractor(), new ReflectionExtractor(), new SerializerExtractor($classMetadataFactory)];

        $propertyExtractor = new PropertyInfoExtractor(typeExtractors: $typeExtractors);

        $objectNormalizer = new ObjectNormalizer(
            $classMetadataFactory,
            $metadataAwareNameConverter,
            $propertyAccessor,
            $propertyExtractor,
        );

        $arrayDenormalizer    = new ArrayDenormalizer();
        $dateTimeDenormalizer = new DateTimeNormalizer();

        return new Serializer(
            [
                $dateTimeDenormalizer,
                $objectNormalizer,
                $arrayDenormalizer,
            ],
            [
                'json' => new JsonEncoder(),
            ]
        );
    }

    public static function validator(): ValidatorInterface
    {
        return Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;
    }
}
