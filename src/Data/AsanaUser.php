<?php
namespace CarloNicora\Minimalism\Services\Asana\Data;

use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaObject;
use CarloNicora\Minimalism\Services\Asana\Commands\AsanaUserCommand;
use Exception;

class AsanaUser extends AbstractAsanaObject
{
    /** @var ?string  */
    private ?string $email=null;

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
     * @throws Exception
     */
    public function getEmail(
    ): string
    {
        if ($this->email === null){
            $this->load();
        }
        
        return $this->email ?? '';
    }
}