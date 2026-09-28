<?php

declare(strict_types=1);

namespace Hyra\Tests\AbnLookup\Unit;

use Hyra\AbnLookup\Exception\SuppressedAbnException;
use Hyra\AbnLookup\Stubs\StubAbnClient;
use PHPUnit\Framework\TestCase;

final class StubAbnClientTest extends TestCase
{
    private const ABN = '12620650553';

    public function testLookupAbnWhenAbnSuppressed(): void
    {
        $client = new StubAbnClient();
        $client->addSuppressedAbns(static::ABN);

        try {
            $response = $client->lookupAbn(static::ABN);
            static::fail(\sprintf('Expected a SuppressedAbnException, got ABN %s', $response->abn));
        } catch (SuppressedAbnException $e) {
            static::assertSame(static::ABN, $e->abn);
        }
    }
}
