<?php

declare(strict_types=1);

namespace Hyra\AbnLookup;

use Hyra\AbnLookup\Exception\AbnNotFoundException;
use Hyra\AbnLookup\Exception\AbrConnectionException;
use Hyra\AbnLookup\Exception\InvalidAbnException;
use Hyra\AbnLookup\Model\AbnResponse;
use Hyra\AbnLookup\Model\NamesResponse;

interface AbnClientInterface
{
    /**
     * @throws InvalidAbnException
     * @throws AbrConnectionException
     * @throws AbnNotFoundException
     */
    public function lookupAbn(string $abn): AbnResponse;

    /**
     * @throws AbrConnectionException
     * @throws InvalidAbnException
     * @throws AbnNotFoundException
     */
    public function lookupName(string $name): NamesResponse;
}
