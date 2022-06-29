<?php
namespace CarloNicora\Minimalism\Services\Asana\Commands;

use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaCommand;
use CarloNicora\Minimalism\Services\Asana\Data\AsanaTag;

class AsanaTagCommand extends AbstractAsanaCommand
{
    /**
     * @param string $workspaceId
     * @return AsanaTag[]
     */
    public function getTags(
        string $workspaceId,
    ): array
    {
        return $this->factory->create(
            type: AsanaTag::class,
            data: $this->client->tags->getTagsForWorkspace(
                workspace_gid: $workspaceId,
            ),
        );
    }
}