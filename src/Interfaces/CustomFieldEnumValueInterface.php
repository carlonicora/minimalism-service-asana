<?php

namespace CarloNicora\Minimalism\Services\Asana\Interfaces;

interface CustomFieldEnumValueInterface
{
    public function __construct(string $id, string $value);
    public function getId(): string;
    public function getValue(): string;
}