<?php

declare(strict_types=1);

namespace Hyra\AbnLookup\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints\NotBlank;

final class Name
{
    #[SerializedName('Abn')]
    #[NotBlank]
    public string $abn;

    #[SerializedName('AbnStatus')]
    #[NotBlank]
    public string $abnStatus;

    #[SerializedName('IsCurrent')]
    #[NotBlank]
    public bool $current;

    #[SerializedName('Name')]
    #[NotBlank]
    public string $name;

    #[SerializedName('NameType')]
    #[NotBlank]
    public string $nameType;

    #[SerializedName('Postcode')]
    #[NotBlank]
    public string $postcode;

    #[SerializedName('State')]
    #[NotBlank]
    public string $state;

    #[SerializedName('Score')]
    #[NotBlank]
    public int $score;
}
