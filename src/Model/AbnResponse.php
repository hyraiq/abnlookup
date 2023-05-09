<?php

declare(strict_types=1);

namespace Hyra\AbnLookup\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

final class AbnResponse extends AbstractResponse
{
    #[SerializedName('Abn')]
    #[NotBlank]
    public string $abn;

    #[SerializedName('AbnStatus')]
    #[NotBlank]
    public string $abnStatus;

    #[SerializedName('AbnStatusEffectiveFrom')]
    #[NotBlank]
    public \DateTimeImmutable $abnStatusEffectiveFrom;

    #[SerializedName('Acn')]
    public ?string $acn = null;

    #[SerializedName('AddressDate')]
    #[NotBlank]
    public \DateTimeImmutable $addressDate;

    #[SerializedName('AddressPostcode')]
    public string $addressPostcode;

    #[SerializedName('AddressState')]
    public string $addressState;

    /**
     * @var string[]
     *
     * @Assert\All({
     *
     *     @Assert\Type("string")
     * })
     */
    #[SerializedName('BusinessName')]
    public array $businessNames = [];

    #[SerializedName('EntityName')]
    #[NotBlank]
    public string $entityName;

    #[SerializedName('EntityTypeCode')]
    #[NotBlank]
    public string $entityTypeCode;

    #[SerializedName('EntityTypeName')]
    #[NotBlank]
    public string $entityTypeName;

    #[SerializedName('Gst')]
    public ?\DateTimeImmutable $gst = null;

    public function isActive(): bool
    {
        return 'active' === \mb_strtolower($this->abnStatus);
    }
}
