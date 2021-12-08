<?php

declare(strict_types=1);

namespace Hyra\AbnLookup\Stubs;

final class MockNamesResponse
{
    /**
     * @return array{Message: string, Names: list<array<string, mixed>>}
     */
    public static function valid(): array
    {
        return [
            'Message' => '',
            'Names'   => [
                [
                    'Abn'       => '12620650553',
                    'AbnStatus' => '0000000001',
                    'IsCurrent' => true,
                    'Name'      => 'Procure Pro',
                    'NameType'  => 'Business Name',
                    'Postcode'  => '4000',
                    'State'     => 'QLD',
                    'Score'     => 100,
                ],
                [
                    'Abn'       => '25607904905',
                    'AbnStatus' => '0000000001',
                    'IsCurrent' => true,
                    'Name'      => 'PROCUREASY PTY LTD',
                    'NameType'  => 'Entity Name',
                    'Postcode'  => '2167',
                    'State'     => 'NSW',
                    'Score'     => 70,
                ],
            ],
        ];
    }

    /**
     * @return array{Message: string, Names: array<string, mixed>}
     */
    public static function noMatches(): array
    {
        return [
            'Message' => '',
            'Names'   => [],
        ];
    }

    /**
     * @return array{Message: string, Names: array<string, mixed>}
     */
    public static function invalidGuid(): array
    {
        return [
            'Message' => 'There was a problem completing your request.',
            'Names'   => [],
        ];
    }
}
