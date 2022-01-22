<?php
namespace CarloNicora\Minimalism\Services\Asana\Data;

use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaObject;
use CarloNicora\Minimalism\Services\Asana\Commands\AsanaSectionCommand;
use CarloNicora\Minimalism\Services\Asana\Commands\AsanaTaskCommand;
use Exception;

class AsanaProject extends AbstractAsanaObject
{
    /** @var AsanaTask[]|null  */
    private ?array $tasks=null;

    /** @var array|null  */
    private ?array $sections=null;

    /**
     * @return void
     * @throws Exception
     */
    protected function loadDetails(
    ): void
    {
        //$data = $this->objectFactory->create(AsanaProjectCommand::class)->loadProject($this->id);
        $this->sections = $this->objectFactory->create(AsanaSectionCommand::class)->loadProjectSections($this->id);
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

    /**
     * @return AsanaSection[]
     * @throws Exception
     */
    public function getSections(
    ): array
    {
        if ($this->sections === null){
            $this->load();
        }
        if ($this->sections === null){
            $this->sections=[];
        }

        return $this->sections;
    }
}