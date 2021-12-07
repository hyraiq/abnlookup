<?php

declare(strict_types=1);

namespace Hyra\AbnLookup\Exception;

final class InvalidGuidException extends AbrConnectionException
{
    public function __construct(
        \Throwable $previous = null
    ) {
        parent::__construct(
            'Invalid ABR GUID specified. Have you added the ABN_LOOKUP_GUID environment variable?',
            $previous
        );
    }
}
