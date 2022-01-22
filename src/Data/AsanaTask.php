<?php
namespace CarloNicora\Minimalism\Services\Asana\Data;

use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaObject;
use CarloNicora\Minimalism\Services\Asana\Commands\AsanaTaskCommand;
use CarloNicora\Minimalism\Services\Asana\Factories\AsanaObjectFactory;
use Exception;
use stdClass;

class AsanaTask extends AbstractAsanaObject
{
    /** @var string  */
    private string $name;

    /** @var bool|null  */
    private ?bool $isCompleted=null;

    /** @var bool|null  */
    private ?bool $isAssigned=null;

    /** @var AsanaUser|null  */
    private ?AsanaUser $assignee=null;

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
        $data = $this->objectFactory->create(AsanaTaskCommand::class)->loadTask($this->id);

        $this->isCompleted = $data->completed;
        $this->isAssigned = $data->assignee !== null;
        $this->assignee = $this->objectFactory->create(AsanaObjectFactory::class)->create(AsanaUser::class, $data->assignee);
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
     * @return bool
     * @throws Exception
     */
    public function isCompleted(): bool
    {
        if ($this->isCompleted === null){
            $this->loadDetails();
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
            $this->loadDetails();
        }

        return $this->assignee;
    }
}