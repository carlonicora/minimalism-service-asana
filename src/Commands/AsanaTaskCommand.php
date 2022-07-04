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
                project_gid: $projectId,
            ),
        );
    }

    /**
     * @param string $userId
     * @return AsanaTask[]
     */
    public function getMyTasks(
        string $userId,
    ): array
    {
        return $this->factory->createFromList(
            type: AsanaTask::class,
            iterator: $this->client->tasks->getTasksForUserTaskList(
                user_task_list_gid: $userId,
                params: [
                    'completed_since' => 'now',
                    'opt_fields' => 'name,assignee_status,assignee_section,assignee_section.name,due_on,due_at',
                ]
            )
        );
    }

    /**
     * @param string $workspaceId
     * @param array $projectIds
     * @param array $teamIds
     * @param string|null $text
     * @param array $tags
     * @param bool $completed
     * @return AsanaTask[]
     */
    public function searchTasks(
        string $workspaceId,
        array $projectIds=[],
        array $teamIds=[],
        ?string $text=null,
        array $tags=[],
        ?bool $completed=null,
    ): array
    {
        $parameters = [];
        if ($projectIds !== []){
            $parameters['projects.any'] = implode(',', $projectIds);
        }

        if ($teamIds !== []){
            $parameters['teams.any'] = implode(',', $teamIds);
        }

        if ($text !== null){
            $parameters['text'] = $text;
        }

        if ($tags !== []){
            $parameters['tags.any'] = implode(',', $tags);
        }

        if ($completed !== null) {
            $parameters['completed'] = $completed;
        }

        return $this->factory->createFromList(
            type: AsanaTask::class,
            iterator: $this->client->tasks->search(
                workspace: $workspaceId,
                params: $parameters,
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

    /**
     * @param AsanaTask $task
     * @return AsanaTask
     * @throws Exception
     */
    public function update(
        AsanaTask $task,
    ): AsanaTask
    {
        $params = [
            'name' => $task->getName(),
        ];

        return $this->factory->create(
            type: AsanaTask::class,
            data: $this->client->tasks->updateTask(
                task_gid: $task->getId(),
                params: $params,
            ),
        );
    }
}