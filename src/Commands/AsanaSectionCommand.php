<?php
namespace CarloNicora\Minimalism\Services\Asana\Commands;

use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaCommand;
use CarloNicora\Minimalism\Services\Asana\Data\AsanaSection;

class AsanaSectionCommand extends AbstractAsanaCommand
{
    /**
     * @param string $projectId
     * @return AsanaSection[]
     */
    public function loadProjectSections(
        string $projectId,
    ): array
    {
        return $this->factory->createFromList(
            type: AsanaSection::class,
            iterator: $this->client->sections->getSectionsForProject(
                project_gid: $projectId,
            ),
        );
    }
}