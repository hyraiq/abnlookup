<?php

declare(strict_types=1);

namespace Hyra\Tests\AbnLookup\Model;

use Faker\Factory;
use Faker\Generator;
use Hyra\AbnLookup\Dependencies;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseModelTest extends TestCase
{
    protected Generator $faker;

    protected ValidatorInterface $validator;

    protected Serializer $serializer;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
        $this->faker->seed();
        $this->validator  = Dependencies::validator();
        $this->serializer = Dependencies::serializer();
    }

    /**
     * @param mixed[]      $data
     * @param class-string $modelClass
     */
    protected function valid(array $data, string $modelClass): void
    {
        try {
            $model         = $this->serializer->denormalize($data, $modelClass);
            $exceptionList = $this->validator->validate($model);
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
        try {
            $model         = $this->serializer->denormalize($data, $modelClass);
            $exceptionList = $this->validator->validate($model);
        } catch (ExceptionInterface $e) {
            $exceptionList = [$e];
        }

        static::assertGreaterThan(0, \count($exceptionList), 'Model should be invalid');
    }
}
