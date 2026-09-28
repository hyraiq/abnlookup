<?php

declare(strict_types=1);

namespace Hyra\AbnLookup\Exception;

/**
 * The ABR hides the details of a suppressed ABN at the holder's request. Only the ABN, its status and its GST
 * registration stay public, so the response has no entity name or type.
 */
final class SuppressedAbnException extends UnexpectedResponseException
{
    public function __construct(
        public readonly string $abn,
        public readonly string $abnStatus,
        public readonly \DateTimeImmutable $abnStatusEffectiveFrom,
        public readonly ?\DateTimeImmutable $gst,
    ) {
        parent::__construct(\sprintf('The ABR has suppressed the details of ABN %s', $abn));
    }
}
