<?php
namespace CarloNicora\Minimalism\Services\Asana\Data;

use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaObject;
use CarloNicora\Minimalism\Services\Asana\Commands\AsanaTaskCommand;
use CarloNicora\Minimalism\Services\Asana\Factories\AsanaObjectFactory;
use Exception;

class AsanaTask extends AbstractAsanaObject
{
    /** @var bool|null  */
    private ?bool $isCompleted=null;

    /** @var bool|null  */
    private ?bool $isAssigned=null;

    /** @var AsanaUser|null  */
    private ?AsanaUser $assignee=null;

    /** @var string|null  */
    private ?string $notes=null;

    /** @var array|null  */
    private ?array $projects=null;

    /**
     * @return void
     * @throws Exception
     */
    protected function loadDetails(
    ): void
    {
        $data = $this->objectFactory->create(AsanaTaskCommand::class)->loadTask($this->id);

        $this->isCompleted = $data->completed;
        $this->isAssigned = $data->assignee !== null;
        $this->assignee = $this->objectFactory->create(AsanaObjectFactory::class)->create(AsanaUser::class, $data->assignee);
        $this->notes = $data->notes ?? '';

        $this->projects = $this->objectFactory->create(AsanaObjectFactory::class)->createFromList(AsanaProject::class, $data->projects);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isCompleted(
    ): bool
    {
        if ($this->isCompleted === null){
            $this->load();
        }

        return $this->isCompleted;
    }

    /**
     * @return AsanaUser|null
     * @throws Exception
     */
    public function getAssignee(
    ): ?AsanaUser
    {
        if ($this->isAssigned === null){
            $this->load();
        }

        return $this->assignee ?? null;
    }

    /**
     * @param AsanaUser $assignee
     */
    public function setAssignee(
        AsanaUser $assignee,
    ): void
    {
        $this->isAssigned = true;
        $this->assignee = $assignee;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getNotes(
    ): string
    {
        if ($this->notes === null){
            $this->load();
        }

        return $this->notes ?? '';
    }

    /**
     * @param string $notes
     */
    public function setNotes(
        string $notes,
    ): void
    {
        $this->notes = $notes;
    }

    /**
     * @return AsanaProject[]
     * @throws Exception
     */
    public function getProjects(
    ): array
    {
        if ($this->projects === null){
            $this->load();
        }
        if ($this->projects === null){
            $this->projects=[];
        }

        return $this->projects;
    }

    /**
     * @param AsanaProject $project
     * @return void
     * @throws Exception
     */
    public function addProject(
        AsanaProject $project,
    ): void
    {
        if ($this->projects === null){
            $this->load();
        }
        if ($this->projects === null){
            $this->projects=[];
        }

        $this->projects[] = $project;
    }
}