<?php

namespace CarloNicora\Minimalism\Services\Asana\Data;

use CarloNicora\Minimalism\Services\Asana\Interfaces\CustomFieldEnumValueInterface;

readonly class CustomFieldEnumValue implements CustomFieldEnumValueInterface
{
    public function __construct(
        private string $id,
        private string $value,
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
