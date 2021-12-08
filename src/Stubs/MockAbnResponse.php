<?php

declare(strict_types=1);

namespace Hyra\AbnLookup\Stubs;

final class MockAbnResponse
{
    /**
     * @return array<string, mixed>
     */
    public static function valid(): array
    {
        return [
            'Abn'                    => '12620650553',
            'AbnStatus'              => 'Active',
            'AbnStatusEffectiveFrom' => '2017-07-24',
            'Acn'                    => '620650553',
            'AddressDate'            => '2017-07-24',
            'AddressPostcode'        => '4000',
            'AddressState'           => 'QLD',
            'BusinessName'           => [
                'Procure Pro',
                'Hyra iQ',
            ],
            'EntityName'     => 'BLENKTECH PTY LTD',
            'EntityTypeCode' => 'PRV',
            'EntityTypeName' => 'Australian Private Company',
            'Gst'            => '2018-04-03',
            'Message'        => '',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function noAbnFound(): array
    {
        return [
            'Abn'                    => '32726197536',
            'AbnStatus'              => '',
            'AbnStatusEffectiveFrom' => '',
            'Acn'                    => '',
            'AddressDate'            => null,
            'AddressPostcode'        => '',
            'AddressState'           => '',
            'BusinessName'           => [],
            'EntityName'             => '',
            'EntityTypeCode'         => '',
            'EntityTypeName'         => '',
            'Gst'                    => null,
            'Message'                => 'No record found',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function invalidAbn(): array
    {
        return [
            'Abn'                    => '',
            'AbnStatus'              => '',
            'AbnStatusEffectiveFrom' => '',
            'Acn'                    => '',
            'AddressDate'            => null,
            'AddressPostcode'        => '',
            'AddressState'           => '',
            'BusinessName'           => [],
            'EntityName'             => '',
            'EntityTypeCode'         => '',
            'EntityTypeName'         => '',
            'Gst'                    => null,
            'Message'                => 'Search text is not a valid ABN or ACN',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function invalidGuid(): array
    {
        return [
            'Abn'                    => '',
            'AbnStatus'              => '',
            'AbnStatusEffectiveFrom' => '',
            'Acn'                    => '',
            'AddressDate'            => null,
            'AddressPostcode'        => '',
            'AddressState'           => '',
            'BusinessName'           => [],
            'EntityName'             => '',
            'EntityTypeCode'         => '',
            'EntityTypeName'         => '',
            'Gst'                    => null,
            'Message'                => 'The GUID entered is not recognised as a Registered Party',
        ];
    }
}
