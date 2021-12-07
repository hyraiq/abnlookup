<?php

declare(strict_types=1);

namespace Hyra\AbnLookup\Exception;

final class InvalidAbnException extends \RuntimeException
{
    public function __construct(
        \Throwable $previous = null
    ) {
        parent::__construct(
            'Invalid ABN',
            0,
            $previous
        );
    }
}
