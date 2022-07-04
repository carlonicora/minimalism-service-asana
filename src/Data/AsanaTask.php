<?php
namespace CarloNicora\Minimalism\Services\Asana\Data;

use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaObject;
use CarloNicora\Minimalism\Services\Asana\Commands\AsanaTaskCommand;
use CarloNicora\Minimalism\Services\Asana\Factories\AsanaObjectFactory;
use DateTime;
use Exception;
use stdClass;

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

    /** @var AsanaSection|null  */
    private ?AsanaSection $assigneeSection=null;

    /** @var string|null  */
    private ?string $dueOn=null;

    /** @var DateTime|null  */
    private ?DateTime $dueAt=null;

    /**
     * @param stdClass|null $data
     * @param ObjectFactory|null $objectFactory
     * @throws Exception
     */
    public function __construct(?stdClass $data = null, ?ObjectFactory $objectFactory = null)
    {
        parent::__construct($data, $objectFactory);

        if ($data?->due_on !== null){
            $this->dueOn = $data?->due_on;
        }
        if ($data?->due_at !== null){
            $this->dueAt = new DateTime($data?->due_at);
        }

        if ($data?->assignee_section !== null) {
            $this->assigneeSection = new AsanaSection($data?->assignee_section);
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
        $this->isAssigned = $data->assignee !== null;
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
}