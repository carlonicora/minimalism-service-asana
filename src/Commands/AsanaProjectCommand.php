<?php
namespace CarloNicora\Minimalism\Services\Asana\Commands;

use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaCommand;
use CarloNicora\Minimalism\Services\Asana\Data\AsanaProject;

class AsanaProjectCommand extends AbstractAsanaCommand
{
    /**
     * @param string $projectId
     * @return AsanaProject
     */
    public function getProject(
        string $projectId,
    ): AsanaProject
    {
        return $this->factory->create(
            type: AsanaProject::class,
            data: $this->client->projects->getProject(
                project_gid: $projectId,
            ),
        );
    }

    /**
     * @param string $teamId
     * @param bool $isArchived
     * @return AsanaProject[]
     */
    public function getProjects(
        string $teamId,
        bool $isArchived=false,
    ): array
    {
        return $this->factory->createFromList(
            type: AsanaProject::class,
            iterator: $this->client->projects->getProjectsForTeam(
                team_gid: $teamId,
                params: ['archived' => $isArchived],
            ),
        );
    }
}