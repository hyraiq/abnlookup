<?php

declare(strict_types=1);

namespace Hyra\AbnLookup\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;

abstract class AbstractResponse
{
    #[SerializedName('Message')]
    public ?string $message = null;
}
