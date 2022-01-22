<?php
namespace CarloNicora\Minimalism\Services\Asana\Abstracts;

use CarloNicora\Minimalism\Factories\ObjectFactory;
use stdClass;

abstract class AbstractAsanaObject
{
    /** @var string  */
    protected string $id;

    /** @var string  */
    private string $name;

    /** @var bool  */
    protected bool $isNew;

    /**
     * @param stdClass|null $data
     * @param ObjectFactory|null $objectFactory
     */
    public function __construct(
        ?stdClass $data=null,
        protected ?ObjectFactory $objectFactory=null,
    )
    {
        $this->isNew = $data===null;

        if ($data !== null) {
            $this->id = $data->gid;
            $this->name = $data->name;
        }
    }

    /**
     * @param string $id
     */
    public function setId(
        string $id,
    ): void
    {
        $this->id = $id;
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
     * @return string
     */
    public function getName(
    ): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(
        string $name,
    ): void
    {
        $this->name = $name;
    }

    /**
     * @return void
     */
    final public function load(
    ): void
    {
        if (!$this->isNew) {
            $this->loadDetails();
        }
    }

    /**
     * @return void
     */
    abstract protected function loadDetails(
    ):void;

    /**
     * @return void
     */
    final public function destroy(
    ): void
    {
        $this->objectFactory = null;
    }

    /**
     * @param ObjectFactory $objectFactory
     * @return void
     */
    final public function initialise(
        ObjectFactory $objectFactory,
    ): void
    {
        $this->objectFactory = $objectFactory;
    }
}