<?php
namespace CarloNicora\Minimalism\Services\Asana\Abstracts;

use CarloNicora\Minimalism\Factories\ObjectFactory;
use stdClass;

abstract class AbstractAsanaObject
{
    /** @var string  */
    protected string $id;

    /**
     * @param ObjectFactory $objectFactory
     * @param stdClass $data
     */
    public function __construct(
        protected ObjectFactory $objectFactory,
        stdClass $data,
    )
    {
        $this->ingest($data);
    }

    /**
     * @param stdClass $data
     */
    protected function ingest(
        stdClass $data,
    ): void
    {
        $this->id = $data->gid;
    }

    /**
     * @return string
     */
    public function getId(
    ): string
    {
        return $this->id;
    }

    /**
     * @return void
     */
    final public function load(
    ): void
    {
        $this->loadDetails();
    }

    /**
     * @return void
     */
    abstract protected function loadDetails(
    ):void;
}