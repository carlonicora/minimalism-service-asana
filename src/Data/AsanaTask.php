<?php
namespace CarloNicora\Minimalism\Services\Asana\Data;

use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaObject;
use CarloNicora\Minimalism\Services\Asana\Commands\AsanaTaskCommand;
use CarloNicora\Minimalism\Services\Asana\Enums\AsanaTaskType;
use CarloNicora\Minimalism\Services\Asana\Factories\AsanaObjectFactory;
use DateTime;
use Exception;
use stdClass;

class AsanaTask extends AbstractAsanaObject
{
    /** @var bool|null  */
    private ?bool $isCompleted=null;

    /** @var AsanaUser|null  */
    private ?AsanaUser $assignee=null;

    /** @var string|null  */
    private ?string $notes=null;

    /** @var array|null  */
    private ?array $projects=null;

    /** @var AsanaSection|null  */
    private ?AsanaSection $assigneeSection=null;

    /** @var string|null  */
    private ?string $dueOn=null;

    /** @var DateTime|null  */
    private ?DateTime $dueAt=null;

    /** @var AsanaTaskType|null  */
    private ?AsanaTaskType $taskType=null;

    /** @var AsanaUser[]|null  */
    private ?array $followers=null;

    /**
     * @param stdClass|null $data
     * @param ObjectFactory|null $objectFactory
     * @throws Exception
     */
    public function __construct(?stdClass $data = null, ?ObjectFactory $objectFactory = null)
    {
        parent::__construct($data, $objectFactory);

        if ($data !== null) {
            if ($data->due_on !== null) {
                $this->dueOn = $data->due_on;
            }
            if ($data->due_at !== null) {
                $this->dueAt = new DateTime($data->due_at);
            }

            if ($data->assignee_section !== null) {
                $this->assigneeSection = new AsanaSection($data->assignee_section);
            }

            if ($data->resource_subtype !== null) {
                $this->taskType = AsanaTaskType::from($data->resource_subtype);
            }
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function loadDetails(
    ): void
    {
        $data = $this->objectFactory->create(AsanaTaskCommand::class)->loadTask($this->id);

        $this->isCompleted = $data->completed;
        $this->assignee = $this->objectFactory->create(AsanaObjectFactory::class)->create(AsanaUser::class, $data->assignee);
        $this->notes = $data->notes ?? '';

        if ($data->assignee_section !== null) {
            $this->assigneeSection = new AsanaSection($data->assignee_section);
        }

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
        return $this->assignee ?? null;
    }

    /**
     * @param AsanaUser $assignee
     */
    public function setAssignee(
        AsanaUser $assignee,
    ): void
    {
        $this->assignee = $assignee;
    }

    /**
     * @param AsanaUser $follower
     * @return void
     */
    public function addFollower(
        AsanaUser $follower,
    ): void
    {
        if ($this->followers === null){
            $this->followers = [];
        }

        $this->followers[] = $follower;
    }

    /**
     * @return AsanaUser[]|null
     */
    public function getFollowers(
    ): ?array
    {
        return $this->followers;
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

    /**
     * @return AsanaSection|null
     */
    public function getAssigneeSection(
    ): ?AsanaSection
    {
        return $this->assigneeSection;
    }

    /**
     * @return string|null
     */
    public function getDueOn(): ?string
    {
        return $this->dueOn;
    }

    /**
     * @return DateTime|null
     */
    public function getDueAt(): ?DateTime
    {
        return $this->dueAt;
    }

    /**
     * @return AsanaTaskType|null
     */
    public function getTaskType(
    ): ?AsanaTaskType
    {
        return $this->taskType;
    }

    /**
     * @param AsanaTaskType|null $taskType
     */
    public function setTaskType(
        ?AsanaTaskType $taskType,
    ): void
    {
        $this->taskType = $taskType;
    }
}