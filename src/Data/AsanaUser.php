<?php
namespace CarloNicora\Minimalism\Services\Asana\Data;

use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaObject;
use CarloNicora\Minimalism\Services\Asana\Commands\AsanaUserCommand;
use Exception;
use stdClass;

class AsanaUser extends AbstractAsanaObject
{
    /** @var string  */
    private string $name;

    /** @var ?string  */
    private ?string $email=null;

    /**
     * @param stdClass $data
     */
    protected function ingest(
        stdClass $data,
    ): void
    {
        parent::ingest($data);

        $this->name = $data->name;
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function loadDetails(
    ): void
    {
        $data = $this->objectFactory->create(AsanaUserCommand::class)->loadUser($this->id);

        $this->email = $data->email ?? null;
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
     * @return string
     * @throws Exception
     */
    public function getEmail(): string
    {
        if ($this->email === null){
            $this->loadDetails();
        }
        
        return $this->email;
    }
}