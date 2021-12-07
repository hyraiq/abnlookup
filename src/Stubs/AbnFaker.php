<?php

declare(strict_types=1);

namespace Hyra\AbnLookup\Stubs;

final class AbnFaker
{
    private function __construct()
    {
    }

    public static function validAbn(): string
    {
        $randomNumber = \str_pad((string) \random_int(1, 999999999), 9, '0', \STR_PAD_LEFT);
        $abn          = '10' . $randomNumber;

        // phpcs:disable Squiz.Arrays.ArrayDeclaration.SingleLineNotAllowed
        $weights = [10, 1, 3, 5, 7, 9, 11, 13, 15, 17, 19];
        $sum     = 0;

        foreach ($weights as $position => $weight) {
            $digit = ((int) $abn[$position]) - (0 !== $position ? 0 : 1);
            $sum += $weight * $digit;
        }

        return ((string) ((89 - ($sum % 89)) + 10)) . $randomNumber;
    }

    public static function invalidAbn(): string
    {
        return \str_pad((string) \random_int(1, 999999999), 10, '0', \STR_PAD_LEFT);
    }
}
