<?php

declare(strict_types=1);

namespace Hyra\Tests\AbnLookup\Unit;

use Hyra\AbnLookup\AbnValidator;
use PHPUnit\Framework\TestCase;

class AbnValidatorTest extends TestCase
{
    /**
     * @dataProvider getValidTests
     */
    public function testValidNumber(string $abn): void
    {
        $result = AbnValidator::isValidNumber($abn);

        static::assertTrue($result);
    }

    /**
     * @dataProvider getInvalidTests
     */
    public function testInvalidNumber(string $abn): void
    {
        $result = AbnValidator::isValidNumber($abn);

        static::assertFalse($result);
    }

    /**
     * @return mixed[]
     */
    public function getValidTests(): array
    {
        return [
            'no spaces'   => ['12620650553'],
            'with dashes' => ['12-620-650-553'],
            'with spaces' => ['12 620 650 553'],
            'random 1'    => ['30 616 935 623'],
            'random 2'    => ['30 618 280 649'],
            'random 3'    => ['80 158 929 938'],
        ];
    }

    /**
     * @return mixed[]
     */
    public function getInvalidTests(): array
    {
        return [
            'less than 11 characters' => ['1234567890'],
            'more than 11 characters' => ['1234567890123456789'],
            'invalid checksum'        => ['54321012345'],
            'letters'                 => ['ab 158 929 938'],
        ];
    }
}
