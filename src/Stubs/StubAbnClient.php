<?php

declare(strict_types=1);

namespace Hyra\AbnLookup\Stubs;

use Hyra\AbnLookup\AbnClientInterface;
use Hyra\AbnLookup\AbnValidator;
use Hyra\AbnLookup\Exception\AbnNotFoundException;
use Hyra\AbnLookup\Exception\InvalidAbnException;
use Hyra\AbnLookup\Model\AbnResponse;
use Hyra\AbnLookup\Model\NamesResponse;

final class StubAbnClient implements AbnClientInterface
{
    /** @var array<string, AbnResponse> */
    private array $abnResponses = [];

    /** @var string[] */
    private array $notFoundAbns = [];

    public function lookupAbn(string $abn): AbnResponse
    {
        if (false === AbnValidator::isValidNumber($abn)) {
            throw new InvalidAbnException();
        }

        if (\array_key_exists($abn, $this->abnResponses)) {
            return $this->abnResponses[$abn];
        }

        if (\in_array($abn, $this->notFoundAbns, true)) {
            throw new AbnNotFoundException();
        }

        throw new \LogicException('Make sure you set a stub response for the abn before calling the AbnClient');
    }

    public function lookupName(string $name): NamesResponse
    {
        return new NamesResponse();
    }

    public function addMockResponse(AbnResponse ...$abnResponses): void
    {
        foreach ($abnResponses as $response) {
            $this->abnResponses[$response->abn] = $response;
        }
    }

    public function addNotFoundAbns(string ...$abns): void
    {
        $this->notFoundAbns = \array_merge(
            $this->notFoundAbns,
            $abns,
        );
    }
}
