<?php
namespace CarloNicora\Minimalism\Services\Asana\Commands;

use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaCommand;
use CarloNicora\Minimalism\Services\Asana\Data\AsanaProject;
use stdClass;

class AsanaProjectCommand extends AbstractAsanaCommand
{
    /**
     * @param string $projectId
     * @return stdClass
     */
    public function loadProject(
        string $projectId,
    ): stdClass
    {
        /** @var stdClass $response */
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $response = $this->client->projects->getProject(
            project_gid: $projectId,
        );

        return $response;
    }

    /**
     * @param string $teamId
     * @return AsanaProject[]
     */
    public function getProjects(
        string $teamId,
    ): array
    {
        return $this->factory->createFromList(
            type: AsanaProject::class,
            iterator: $this->client->projects->getProjectsForTeam(
                team_gid: $teamId,
            ),
        );
    }
}