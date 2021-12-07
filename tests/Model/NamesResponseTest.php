<?php

declare(strict_types=1);

namespace Hyra\Tests\AbnLookup\Model;

use Hyra\AbnLookup\Model\NamesResponse;
use Hyra\AbnLookup\Stubs\MockNamesResponse;

final class NamesResponseTest extends BaseModelTest
{
    /**
     * @dataProvider getValidTests
     *
     * @param array<array-key, mixed> $payload
     */
    public function testValid(array $payload): void
    {
        $this->valid($payload, NamesResponse::class);
    }

    /**
     * @dataProvider getInvalidTests
     *
     * @param array<array-key, mixed> $payload
     */
    public function testInvalid(array $payload): void
    {
        $this->invalid($payload, NamesResponse::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function getValidTests(): array
    {
        return [
            'with matches'    => [MockNamesResponse::valid()],
            'without matches' => [MockNamesResponse::noMatches()],
        ];
    }

    /**
     * @return \Generator<string, mixed>
     */
    public function getInvalidTests(): \Generator
    {
        $properties = [
            'Abn',
            'AbnStatus',
            'IsCurrent',
            'Name',
            'NameType',
            'Postcode',
            'State',
            'Score',
        ];

        foreach ($properties as $key) {
            $fields = MockNamesResponse::valid();
            unset($fields['Names'][0][$key]);
            yield \sprintf('missing %s', $key) => [$fields];
        }
    }
}
