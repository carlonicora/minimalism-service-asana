<?php
namespace CarloNicora\Minimalism\Services\Asana\Data;

use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaObject;
use CarloNicora\Minimalism\Services\Asana\Commands\AsanaTaskCommand;
use Exception;
use stdClass;

class AsanaProject extends AbstractAsanaObject
{
    /** @var string  */
    private string $name;

    /** @var AsanaTask[]|null  */
    private ?array $tasks=null;

    /**
     * @param stdClass $data
     */
    protected function ingest(
        stdClass $data,
    ): void
    {
        parent::ingest($data);

        $this->name = $data->name;
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function loadDetails(
    ): void
    {
        //$data = $this->objectFactory->create(AsanaProjectCommand::class)->loadProject($this->id);
    }

    /**
     * @return string
     */
    public function getName(
    ): string
    {
        return $this->name;
    }

    /**
     * @return AsanaTask[]
     * @throws Exception
     */
    public function getTasks(
    ): array
    {
        if ($this->tasks === null){
            $this->tasks = $this->objectFactory->create(AsanaTaskCommand::class)->getTasksFromProject(
                projectId: $this->id
            );
        }

        return $this->tasks;
    }
}