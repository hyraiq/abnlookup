<?php

declare(strict_types=1);

namespace Hyra\Tests\AbnLookup\Integration;

use Faker\Factory;
use Faker\Generator;
use Hyra\AbnLookup\AbnClient;
use Hyra\AbnLookup\Dependencies;
use Hyra\AbnLookup\Exception\AbnNotFoundException;
use Hyra\AbnLookup\Exception\AbrConnectionException;
use Hyra\AbnLookup\Exception\InvalidAbnException;
use Hyra\AbnLookup\Exception\InvalidGuidException;
use Hyra\AbnLookup\Exception\UnexpectedResponseException;
use Hyra\AbnLookup\Model\Name;
use Hyra\AbnLookup\Stubs\MockAbnResponse;
use Hyra\AbnLookup\Stubs\MockNamesResponse;
use Hyra\AbnLookup\Stubs\StubAbnHttpClient;
use PHPUnit\Framework\TestCase;

final class AbnClientTest extends TestCase
{
    protected Generator $faker;

    private const ABN = '12620650553';

    private AbnClient $client;

    private StubAbnHttpClient $stubHttpClient;

    private string $guid;

    protected function setUp(): void
    {
        $this->faker          = Factory::create();
        $denormalizer         = Dependencies::serializer();
        $validator            = Dependencies::validator();
        $this->stubHttpClient = new StubAbnHttpClient();
        $this->guid           = $this->faker->uuid;

        $this->client = new AbnClient($denormalizer, $validator, $this->stubHttpClient, $this->guid);
    }

    /**
     * Yes, this is a bad test. It just reimplements logic in AbnClient. However, we want to ensure the defaults
     * don't change.
     */
    public function testClientInitialisedCorrectly(): void
    {
        $this->stubHttpClient->assertDefaultOptions([
            'base_uri' => 'https://abr.business.gov.au/json/',
            'query'    => [
                'guid'     => $this->guid,
                'callback' => 'callback',
            ],
        ]);
    }

    public function testLookupAbnInvalidAbnDoesNotUseApi(): void
    {
        $this->expectException(InvalidAbnException::class);

        $this->client->lookupAbn($this->faker->numerify('#####'));

        $this->stubHttpClient->assertAbnDetailsNotCalled();
    }

    public function testLookupAbnConnectionExceptionOnNon200Response(): void
    {
        $this->stubHttpClient->setStubResponse([], 500);

        $this->expectException(AbrConnectionException::class);

        $this->client->lookupAbn(static::ABN);
    }

    public function testLookupAbnWhenAbnNotFound(): void
    {
        $this->stubHttpClient->setStubResponse(MockAbnResponse::noAbnFound());

        $this->expectException(AbnNotFoundException::class);

        $this->client->lookupAbn(static::ABN);
    }

    public function testLookupAbnWithInvalidAbn(): void
    {
        $this->stubHttpClient->setStubResponse(MockAbnResponse::invalidAbn());

        $this->expectException(InvalidAbnException::class);

        $this->client->lookupAbn(static::ABN);
    }

    public function testLookupAbnWithInvalidGuid(): void
    {
        $this->stubHttpClient->setStubResponse(MockAbnResponse::invalidGuid());

        $this->expectException(InvalidGuidException::class);

        $this->client->lookupAbn(static::ABN);
    }

    public function testLookupAbnHandlesUnexpectedResponse(): void
    {
        $response        = MockAbnResponse::valid();
        $response['Abn'] = null;
        $this->stubHttpClient->setStubResponse($response);

        $this->expectException(UnexpectedResponseException::class);

        $this->client->lookupAbn(static::ABN);
    }

    public function testLookupAbnSuccess(): void
    {
        $mockResponse = MockAbnResponse::valid();
        $this->stubHttpClient->setStubResponse($mockResponse);

        $response = $this->client->lookupAbn(static::ABN);

        $normalizedResponse = [
            'Abn'                    => $response->abn,
            'AbnStatus'              => $response->abnStatus,
            'AbnStatusEffectiveFrom' => $response->abnStatusEffectiveFrom->format('Y-m-d'),
            'Acn'                    => $response->acn,
            'AddressDate'            => $response->addressDate->format('Y-m-d'),
            'AddressPostcode'        => $response->addressPostcode,
            'AddressState'           => $response->addressState,
            'BusinessName'           => $response->businessNames,
            'EntityName'             => $response->entityName,
            'EntityTypeCode'         => $response->entityTypeCode,
            'EntityTypeName'         => $response->entityTypeName,
            'Gst'                    => $response->gst?->format('Y-m-d'),
            'Message'                => $response->message,
        ];

        static::assertSame($mockResponse, $normalizedResponse);
    }

    public function testLookupNameConnectionExceptionOnNon200Response(): void
    {
        $this->stubHttpClient->setStubResponse([], 500);

        $this->expectException(AbrConnectionException::class);

        $this->client->lookupName($this->faker->word);
    }

    public function testLookupNameWithInvalidGuid(): void
    {
        $this->stubHttpClient->setStubResponse(MockNamesResponse::invalidGuid());

        $this->expectException(InvalidGuidException::class);

        $this->client->lookupName($this->faker->word);
    }

    /**
     * @dataProvider dataProviderNamesMockResponse
     *
     * @param mixed[] $mockResponse
     */
    public function testLookupNamesSuccess(array $mockResponse): void
    {
        $this->stubHttpClient->setStubResponse($mockResponse);

        $response = $this->client->lookupName($this->faker->word);

        $normalizedResponse = [
            'Message' => $response->message,
            'Names'   => \array_map(fn (Name $name) => [
                'Abn'       => $name->abn,
                'AbnStatus' => $name->abnStatus,
                'IsCurrent' => $name->current,
                'Name'      => $name->name,
                'NameType'  => $name->nameType,
                'Postcode'  => $name->postcode,
                'State'     => $name->state,
                'Score'     => $name->score,
            ], $response->names),
        ];

        static::assertSame($mockResponse, $normalizedResponse);
    }

    /**
     * @return array<string, mixed>
     */
    public function dataProviderNamesMockResponse(): array
    {
        return [
            'with matches'    => [MockNamesResponse::valid()],
            'without matches' => [MockNamesResponse::noMatches()],
        ];
    }
}
