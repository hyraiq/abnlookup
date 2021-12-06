<?php

declare(strict_types=1);

namespace Hyra\AbnLookup;

final class AbnValidator
{
    public static function isValidNumber(string $abn): bool
    {
        // Replace whitespace and hyphens
        $abn = \preg_replace('/[\s-]+/', '', $abn);
        if (null === $abn) {
            return false;
        }

        // Validate length and digits
        if (1 !== \preg_match('/^\d{11}$/', $abn)) {
            return false;
        }

        $weights = [10, 1, 3, 5, 7, 9, 11, 13, 15, 17, 19];
        $sum     = 0;

        /** @psalm-var int<0, 10> $key */
        foreach (\mb_str_split($abn) as $key => $digit) {
            // The first digit is reduced by 1
            $number = ((int) $digit) - (0 === $key ? 1 : 0);
            $sum += $number * $weights[$key];
        }

        return 0 === ($sum % 89);
    }
}
