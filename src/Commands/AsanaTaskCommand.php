<?php
namespace CarloNicora\Minimalism\Services\Asana\Commands;

use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaCommand;
use CarloNicora\Minimalism\Services\Asana\Data\AsanaTask;
use Exception;
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

    /**
     * @param AsanaTask $task
     * @return AsanaTask
     * @throws Exception
     */
    public function create(
        AsanaTask $task,
    ): AsanaTask
    {
        $taskProjects=[];
        foreach ($task->getProjects() as $project){
            $taskProjects[] = $project->getId();
        }

        $params = [
            'name' => $task->getName(),
            'notes' => $task->getNotes(),
            'projects' => $taskProjects,
        ];

        if ($task->getAssignee() !== null){
            $params['assignee'] = $task->getAssignee()->getId();
        }

        return $this->factory->create(
            type: AsanaTask::class,
            data: $this->client->tasks->createTask(
                params: $params,
            ),
        );
    }
}