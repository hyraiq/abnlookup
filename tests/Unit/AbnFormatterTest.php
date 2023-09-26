<?php

declare(strict_types=1);

namespace Hyra\Tests\AbnLookup\Unit;

use Hyra\AbnLookup\AbnFormatter;
use PHPUnit\Framework\TestCase;

class AbnFormatterTest extends TestCase
{
    public function testFormatter(): void
    {
        $abn          = '30616935623';
        $formattedAbn = '30 616 935 623';

        $result = AbnFormatter::format($abn);

        static::assertSame($result, $formattedAbn);
    }
}
