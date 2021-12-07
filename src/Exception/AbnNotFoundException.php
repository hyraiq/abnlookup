<?php

declare(strict_types=1);

namespace Hyra\AbnLookup\Exception;

final class AbnNotFoundException extends \RuntimeException
{
    public function __construct(
        \Throwable $previous = null
    ) {
        parent::__construct(
            'ABN not found',
            0,
            $previous
        );
    }
}
