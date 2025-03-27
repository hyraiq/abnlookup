<?php

declare(strict_types=1);

namespace Hyra\AbnLookup;

use Hyra\AbnLookup\Exception\AbnNotFoundException;
use Hyra\AbnLookup\Exception\AbrConnectionException;
use Hyra\AbnLookup\Exception\InvalidAbnException;
use Hyra\AbnLookup\Exception\InvalidGuidException;
use Hyra\AbnLookup\Exception\UnexpectedResponseException;
use Hyra\AbnLookup\Model\AbnResponse;
use Hyra\AbnLookup\Model\AbstractResponse;
use Hyra\AbnLookup\Model\NamesResponse;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class AbnClient implements AbnClientInterface
{
    private HttpClientInterface $client;

    public function __construct(
        private DenormalizerInterface $denormalizer,
        private ValidatorInterface $validator,
        HttpClientInterface $client,
        string $abnLookupGuid,
        string $abnLookupBaseApiUri = 'https://abr.business.gov.au/json/',
    ) {
        $this->client = $client->withOptions([
            'base_uri' => $abnLookupBaseApiUri,
            'query'    => [
                'guid'     => $abnLookupGuid,
                'callback' => 'callback',
            ],
        ]);
    }

    /**
     * @throws InvalidAbnException
     * @throws AbrConnectionException
     * @throws AbnNotFoundException
     */
    public function lookupAbn(string $abn): AbnResponse
    {
        if (false === AbnValidator::isValidNumber($abn)) {
            throw new InvalidAbnException();
        }

        try {
            $response = $this->client->request(
                'GET',
                'AbnDetails.aspx',
                [
                    'query' => [
                        'abn' => $abn,
                    ],
                ]
            )->getContent();
        } catch (HttpExceptionInterface $e) {
            throw new AbrConnectionException(\sprintf('Unable to connect to the ABR: %s', $e->getMessage()), $e);
        }

        /** @var AbnResponse $model */
        $model = $this->decodeResponse($response, AbnResponse::class);

        // Null is better than empty string
        if ('' === $model->acn) {
            $model->acn = null;
        }

        return $model;
    }

    /**
     * @throws AbrConnectionException
     * @throws InvalidAbnException
     * @throws AbnNotFoundException
     */
    public function lookupName(string $name): NamesResponse
    {
        try {
            $response = $this->client->request(
                'GET',
                'MatchingNames.aspx',
                [
                    'query' => [
                        'name' => $name,
                    ],
                ]
            )->getContent();
        } catch (HttpExceptionInterface $e) {
            throw new AbrConnectionException(\sprintf('Unable to connect to the ABR: %s', $e->getMessage()), $e);
        }

        /** @var NamesResponse $model */
        $model = $this->decodeResponse($response, NamesResponse::class);

        return $model;
    }

    /**
     * @template T of AbstractResponse
     *
     * @psalm-param    class-string<T> $type
     *
     * @psalm-return   T
     *
     * @throws AbnNotFoundException
     * @throws InvalidAbnException
     * @throws AbrConnectionException
     */
    private function decodeResponse(string $response, string $type): object
    {
        $body = $this->decodeJsonp($response);
        $this->checkResponseMessage($body);

        try {
            /** @psalm-var T $model */
            $model = $this->denormalizer->denormalize($body, $type, 'json');
        } catch (SerializerExceptionInterface $e) {
            throw new UnexpectedResponseException(
                \sprintf('Unable to deserialize response from the ABR "%s": %s', $response, $e->getMessage()),
                $e
            );
        }

        $violations = $this->validator->validate($model);

        if (0 < \count($violations)) {
            $errors = \array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getPropertyPath(),
                \iterator_to_array($violations)
            );

            throw new UnexpectedResponseException(
                \sprintf('ABR response contains errors "%s": %s', $response, \json_encode($errors))
            );
        }

        return $model;
    }

    /**
     * @param mixed[] $data
     *
     * @throws AbnNotFoundException
     * @throws InvalidGuidException
     * @throws InvalidAbnException
     * @throws UnexpectedResponseException
     */
    private function checkResponseMessage(array $data): void
    {
        if (false === \array_key_exists('Message', $data)) {
            throw new UnexpectedResponseException(
                \sprintf('The ABR did not return a message property: %s', \json_encode($data))
            );
        }

        $message = $data['Message'];

        if ('No record found' === $message) {
            throw new AbnNotFoundException();
        }

        if ('Search text is not a valid ABN or ACN' === $message) {
            throw new InvalidAbnException();
        }

        if ('The GUID entered is not recognised as a Registered Party' === $message) {
            throw new InvalidGuidException();
        }

        if ('There was a problem completing your request.' === $message) {
            throw new InvalidGuidException();
        }
    }

    /**
     * @throws UnexpectedResponseException
     *
     * @return mixed[]
     */
    private function decodeJsonp(string $jsonp): array
    {
        $matches = [];
        \preg_match('/^callback\((.*)\)$/', $jsonp, $matches);

        if (2 !== \count($matches)) {
            throw new UnexpectedResponseException(\sprintf('Invalid response from the ABR: %s', $jsonp));
        }

        $body = $matches[1];

        try {
            /** @var mixed[] $data */
            $data = \json_decode($body, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new UnexpectedResponseException(
                \sprintf('The ABR returned a non-JSON result "%s": %s', $body, $e->getMessage()),
                $e
            );
        }

        return $data;
    }
}
