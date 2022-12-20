<?php

namespace CarloNicora\Minimalism\Services\Asana\Data;

use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaObject;
use CarloNicora\Minimalism\Services\Asana\Interfaces\CustomFieldEnumValueInterface;
use Exception;
use stdClass;

class AsanaCustomField extends AbstractAsanaObject
{
    /** @var string  */
    private string $custom_field_gid;

    /** @var mixed  */
    private mixed $simpleValue=null;

    /** @var CustomFieldEnumValueInterface[]|null  */
    private ?array $multiValues=null;

    public function __construct(
        ?stdClass      $data = null,
        ?ObjectFactory $objectFactory = null)
    {
        parent::__construct($data, $objectFactory);

        if ($data !== null){
            $this->custom_field_gid = $data->gid;

            if ($data->enum_options !== null && $data->enum_options !== []){
                $this->multiValues = [];

                if ($data->multi_enum_values !== null && $data->multi_enum_values !== []){
                    foreach ($data->multi_enum_values as $value){
                        $this->multiValues[] = new CustomFieldEnumValue($value->gid, $value->name);
                    }
                }
            } else {
                $this->simpleValue = $data->text_value;
            }
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function loadDetails(
    ): void
    {
    }

    public function id(
    ): string {
        return $this->custom_field_gid;
    }

    public function value(
    ): mixed {
        return $this->multiValues ?? $this->simpleValue;
    }
}