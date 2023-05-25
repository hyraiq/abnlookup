<?php

declare(strict_types=1);

namespace Hyra\AbnLookup\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final class NamesResponse extends AbstractResponse
{
    /**
     * @var Name[]
     */
    #[SerializedName('Names')]
    #[Assert\Valid]
    #[Assert\All(constraints: new Assert\Type(Name::class))]
    public array $names = [];
}
