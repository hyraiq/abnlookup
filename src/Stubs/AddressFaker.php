<?php

declare(strict_types=1);

namespace Hyra\AbnLookup\Stubs;

use Faker\Factory;

final class AddressFaker
{
    private function __construct()
    {
    }

    /**
     * @return array<string, string>
     */
    public static function validAddress(): array
    {
        $faker = Factory::create();

        /** @var string $subdivision */
        $subdivision = $faker->randomElement(['QLD', 'NSW', 'VIC', 'TAS', 'NT', 'SA', 'WA']);

        return [
            'line1'       => $faker->address,
            'line2'       => $faker->secondaryAddress,
            'city'        => $faker->city,
            'subdivision' => $subdivision,
            'country'     => 'AU',
            'postcode'    => $faker->numerify('####'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function invalidAddress(): array
    {
        $model            = static::validAddress();
        $model['country'] = 'US';

        return $model;
    }
}
