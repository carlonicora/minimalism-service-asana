<?php
namespace CarloNicora\Minimalism\Services\Asana\Commands;

use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaCommand;
use CarloNicora\Minimalism\Services\Asana\Data\AsanaTask;
use stdClass;

class AsanaTaskCommand extends AbstractAsanaCommand
{
    /**
     * @param string $taskId
     * @return stdClass
     */
    public function loadTask(
        string $taskId,
    ): stdClass
    {
        /** @var stdClass $response */
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $response = $this->client->tasks->getTask(
            task_gid: $taskId,
        );

        return $response;
    }

    /**
     * @param string $projectId
     * @return AsanaTask[]
     */
    public function getTasksFromProject(
        string $projectId,
    ): array
    {
        return $this->factory->createFromList(
            type: AsanaTask::class,
            iterator: $this->client->tasks->getTasksForProject(
                project_gid: $projectId
            ),
        );
    }
}