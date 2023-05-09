<?php

declare(strict_types=1);

namespace Hyra\AbnLookup\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final class NamesResponse extends AbstractResponse
{
    /**
     * @var Name[]
     *
     * @Assert\All({
     *
     *     @Assert\Type("Hyra\AbnLookup\Model\Name")
     * })
     */
    #[SerializedName('Names')]
    #[Assert\Valid]
    public array $names = [];
}
