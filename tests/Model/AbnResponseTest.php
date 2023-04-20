<?php

declare(strict_types=1);

namespace Hyra\Tests\AbnLookup\Model;

use Hyra\AbnLookup\Model\AbnResponse;
use Hyra\AbnLookup\Stubs\MockAbnResponse;

final class AbnResponseTest extends BaseModelTest
{
    /**
     * @dataProvider getValidTests
     *
     * @param array<array-key, mixed> $payload
     */
    public function testValid(array $payload): void
    {
        $this->valid($payload, AbnResponse::class);
    }

    /**
     * @dataProvider getInvalidTests
     *
     * @param array<array-key, mixed> $payload
     */
    public function testInvalid(array $payload): void
    {
        $this->invalid($payload, AbnResponse::class);
    }

    /**
     * @return \Generator<string, mixed>
     */
    public function getValidTests(): \Generator
    {
        yield 'all fields' => [MockAbnResponse::valid()];

        $fields        = MockAbnResponse::valid();
        $fields['Acn'] = '';
        yield 'no acn' => [$fields];

        $fields                 = MockAbnResponse::valid();
        $fields['BusinessName'] = [];
        yield 'no business names' => [$fields];

        $fields        = MockAbnResponse::valid();
        $fields['Gst'] = null;
        yield 'no gst' => [$fields];

        $fields                 = MockAbnResponse::valid();
        $fields['AddressState'] = '';
        yield 'empty string for address state' => [$fields];

        $fields                    = MockAbnResponse::valid();
        $fields['AddressPostcode'] = '';
        yield 'empty string for address postcode' => [$fields];
    }

    /**
     * @return \Generator<string, mixed>
     */
    public function getInvalidTests(): \Generator
    {
        $fields                           = MockAbnResponse::valid();
        $fields['AbnStatusEffectiveFrom'] = '';
        yield 'invalid effective date' => [$fields];

        $fields                           = MockAbnResponse::valid();
        $fields['AbnStatusEffectiveFrom'] = '';
        $fields['AddressDate']            = '';
        yield 'invalid address date' => [$fields];

        $fields                           = MockAbnResponse::valid();
        $fields['AbnStatusEffectiveFrom'] = '';
        $fields['BusinessName']           = [
            1,
            2,
        ];
        yield 'invalid business names' => [$fields];

        $fields                           = MockAbnResponse::valid();
        $fields['AbnStatusEffectiveFrom'] = '';
        $fields['Gst']                    = '';
        yield 'invalid GST' => [$fields];

        yield 'no abn found' => [MockAbnResponse::noAbnFound()];
        yield 'invalid abn' => [MockAbnResponse::invalidAbn()];
        yield 'invalid guid' => [MockAbnResponse::invalidGuid()];

        $requiredProperties = [
            'Abn',
            'AbnStatus',
            'AbnStatusEffectiveFrom',
            'AddressDate',
            'EntityName',
            'EntityTypeCode',
            'EntityTypeName',
        ];

        foreach ($requiredProperties as $key) {
            $fields = MockAbnResponse::valid();
            unset($fields[$key]);
            yield \sprintf('missing %s', $key) => [$fields];
        }
    }
}
