<?php

declare(strict_types=1);

namespace Hyra\AbnLookup\Stubs;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

final class StubAbnHttpClient implements HttpClientInterface
{
    /** @var null|mixed[] */
    private ?array $defaultOptions = null;

    private ?MockResponse $stubResponse = null;

    /** @var null|mixed[] */
    private ?array $abnDetailsOptions = null;

    /** @var null|mixed[] */
    private ?array $matchingNamesOptions = null;

    /**
     * @param mixed[] $options
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        if ('GET' !== $method) {
            throw new \LogicException('Not implemented: AbnClient should only use GET requests');
        }

        return match ($url) {
            'AbnDetails.aspx'    => $this->handleAbnDetailsRequest($options),
            'MatchingNames.aspx' => $this->handleMatchingNamesRequest($options),
            default              => throw new \LogicException(
                'Not implemented: AbnClient only responds to AbnDetails and MatchingNames'
            )
        };
    }

    public function stream($responses, float $timeout = null): ResponseStreamInterface
    {
        throw new \LogicException('Not implemented: AbnClient should only be using the request method');
    }

    /**
     * @param mixed[] $options
     */
    public function withOptions(array $options): static
    {
        $this->defaultOptions = $options;

        return $this;
    }

    /**
     * @param mixed[] $data
     */
    public function setStubResponse(array $data, int $statusCode = 200): void
    {
        try {
            $responseContent = \sprintf('callback(%s)', \json_encode($data, \JSON_THROW_ON_ERROR));
        } catch (\JsonException $e) {
            throw new \LogicException(\sprintf('Unable to json_encode $data: %s', $e->getMessage()), 0, $e);
        }

        $this->stubResponse = new MockResponse(
            $responseContent,
            ['http_code' => $statusCode]
        );
    }

    /**
     * @param mixed[] $expectedOptions
     */
    public function assertDefaultOptions(array $expectedOptions): void
    {
        TestCase::assertSame($expectedOptions, $this->defaultOptions);
    }

    /**
     * @param mixed[] $expectedOptions
     */
    public function assertAbnDetailsCalled(array $expectedOptions): void
    {
        TestCase::assertSame($expectedOptions, $this->abnDetailsOptions);
    }

    public function assertAbnDetailsNotCalled(): void
    {
        if (null !== $this->abnDetailsOptions) {
            TestCase::fail('AbnDetails should not have been called');
        }
    }

    /**
     * @param mixed[] $expectedOptions
     */
    public function assertMatchingNamesCalled(array $expectedOptions): void
    {
        TestCase::assertSame($expectedOptions, $this->matchingNamesOptions);
    }

    public function assertMatchingNamesNotCalled(): void
    {
        if (null !== $this->matchingNamesOptions) {
            TestCase::fail('MatchingNames should not have been called');
        }
    }

    /**
     * @param mixed[] $options
     */
    private function handleAbnDetailsRequest(array $options): ResponseInterface
    {
        if (null !== $this->abnDetailsOptions) {
            throw new \LogicException('Not implemented: the AbnClient should only be called once');
        }

        $this->abnDetailsOptions = $options;

        // @phpstan-ignore-next-line - no, a mock http client will not throw TransportException
        return $this->getMockHttpClient()->request('GET', 'AbnDetails.aspx', $options);
    }

    /**
     * @param mixed[] $options
     */
    private function handleMatchingNamesRequest(array $options): ResponseInterface
    {
        if (null !== $this->matchingNamesOptions) {
            throw new \LogicException('Not implemented: the AbnClient should only be called once');
        }

        $this->matchingNamesOptions = $options;

        // @phpstan-ignore-next-line - no, a mock http client will not throw TransportException
        return $this->getMockHttpClient()->request('GET', 'MatchingNames.aspx', $options);
    }

    private function getMockHttpClient(): MockHttpClient
    {
        if (null === $this->stubResponse) {
            throw new \LogicException('You must set the stub response before calling the AbnClient');
        }

        return new MockHttpClient($this->stubResponse, 'https://abr.business.gov.au/json/');
    }
}
