<?php
namespace CarloNicora\Minimalism\Services\Asana\Factories;

use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Interfaces\SimpleObjectInterface;
use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaObject;

class AsanaObjectFactory implements SimpleObjectInterface
{
    /**
     * @param ObjectFactory $objectFactory
     */
    public function __construct(
        protected ObjectFactory $objectFactory,
    )
    {
    }

    /**
     * @template InstanceOfType
     * @param class-string<InstanceOfType> $type
     * @param mixed $iterator
     * @return InstanceOfType[]
     */
    public function createFromList(
        string $type,
        mixed $iterator,
    ): array
    {
        $response = [];

        foreach ($iterator as $object){
            $response[] = $this->create(
                type: $type,
                data: $object,
            );
        }

        return $response;
    }

    /**
     * @template InstanceOfType
     * @param class-string<InstanceOfType> $type
     * @param mixed $data
     * @return InstanceOfType
     */
    public function create(
        string $type,
        mixed $data,
    ): AbstractAsanaObject
    {
        return new $type(
            objectFactory: $this->objectFactory,
            data:$data,
        );
    }
}