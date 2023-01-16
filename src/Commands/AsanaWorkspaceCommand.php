<?php
namespace CarloNicora\Minimalism\Services\Asana\Commands;

use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaCommand;
use stdClass;

class AsanaWorkspaceCommand extends AbstractAsanaCommand
{
    /**
     * @param string $workspaceId
     * @return stdClass
     */
    public function loadWorkspace(
        string $workspaceId,
    ): stdClass
    {
        /** @var stdClass $response */
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $response = $this->client->workspaces->getWorkspace(
            workspace_gid: $workspaceId,
        );

        return $response;
    }
}