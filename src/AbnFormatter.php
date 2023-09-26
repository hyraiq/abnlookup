<?php

declare(strict_types=1);

namespace Hyra\AbnLookup;

final class AbnFormatter
{
    public static function format(string $abn): string
    {
        return \sprintf(
            '%s %s %s %s',
            \mb_substr($abn, 0, 2),
            \mb_substr($abn, 2, 3),
            \mb_substr($abn, 5, 3),
            \mb_substr($abn, 8, 3)
        );
    }
}
