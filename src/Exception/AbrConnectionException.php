<?php

declare(strict_types=1);

namespace Hyra\AbnLookup\Exception;

class AbrConnectionException extends \RuntimeException
{
    public function __construct(
        string $message,
        \Throwable $previous = null
    ) {
        parent::__construct(
            $message,
            0,
            $previous
        );
    }
}
