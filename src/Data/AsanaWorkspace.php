<?php
namespace CarloNicora\Minimalism\Services\Asana\Data;

use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaObject;
use Exception;

class AsanaWorkspace extends AbstractAsanaObject
{
    /**
     * @return void
     * @throws Exception
     */
    protected function loadDetails(
    ): void
    {
        //$data = $this->objectFactory->create(AsanaWorkspaceCommand::class)->loadWorkspace($this->id);
    }
}