<?php

declare(strict_types=1);

namespace Hyra\Tests\AbnLookup\Model;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validation;

abstract class BaseModelTest extends TestCase
{
    protected Generator $faker;

    /**
     * @param mixed[] $data
     *
     * @psalm-suppress MissingParamType
     * @psalm-suppress InternalMethod
     */
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->faker = Factory::create();
        $this->faker->seed();
    }

    /**
     * @param mixed[]      $data
     * @param class-string $modelClass
     */
    protected function valid(array $data, string $modelClass): void
    {
        $validator = Validation::createValidator();

        try {
            $model         = $this->denormalize($data, $modelClass);
            $exceptionList = $validator->validate($model);
        } catch (ExceptionInterface $e) {
            throw new \LogicException(
                \sprintf('Unable to denormalise into %s: %s', $modelClass, $e->getMessage()),
                0,
                $e
            );
        }

        $errors = \array_map(
            fn (ConstraintViolationInterface $violation) => \sprintf(
                '%s: %s',
                $violation->getPropertyPath(),
                (string) $violation->getMessage()
            ),
            \iterator_to_array($exceptionList)
        );
        static::assertSame([], $errors, 'Model should be valid');
    }

    /**
     * @param mixed[]      $data
     * @param class-string $modelClass
     */
    protected function invalid(array $data, string $modelClass): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator();

        try {
            $model         = $this->denormalize($data, $modelClass);
            $exceptionList = $validator->validate($model);
        } catch (ExceptionInterface $e) {
            $exceptionList = [$e];
        }

        static::assertGreaterThan(0, \count($exceptionList), 'Model should be invalid');
    }

    /**
     * @param mixed[]      $data
     * @param class-string $modelClass
     *
     * @throws ExceptionInterface
     */
    private function denormalize(array $data, string $modelClass): object
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);
        $propertyAccessor = new PropertyAccessor();

        $propertyExtractor = new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()]);

        $objectNormalizer = new ObjectNormalizer(
            $classMetadataFactory,
            $metadataAwareNameConverter,
            $propertyAccessor,
            $propertyExtractor,
        );

        $arrayDenormalizer    = new ArrayDenormalizer();
        $dateTimeDenormalizer = new DateTimeNormalizer();

        $serializer = new Serializer(
            [
                $dateTimeDenormalizer,
                $objectNormalizer,
                $arrayDenormalizer,
            ],
            [
                'json' => new JsonEncoder(),
            ]
        );

        /** @var object $model */
        $model = $serializer->denormalize($data, $modelClass);

        return $model;
    }
}
