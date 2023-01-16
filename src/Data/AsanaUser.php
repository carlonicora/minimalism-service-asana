<?php
namespace CarloNicora\Minimalism\Services\Asana\Data;

use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaObject;
use CarloNicora\Minimalism\Services\Asana\Commands\AsanaUserCommand;
use Exception;
use stdClass;

class AsanaUser extends AbstractAsanaObject
{
    /** @var string|null  */
    private ?string $email;

    /** @var string|null  */
    private ?string $avatar;

    /** @var AsanaWorkspace[]  */
    private array $workspaces=[];

    /**
     * @param stdClass|null $data
     * @param ObjectFactory|null $objectFactory
     */
    public function __construct(
        ?stdClass $data=null,
        protected ?ObjectFactory $objectFactory=null,
    )
    {
        parent::__construct($data, $this->objectFactory);

        $this->email = $data->email ?? null;
        $this->avatar = $data->photo->image_128x128 ?? null;

        if (!empty($data?->workspaces)){
            foreach ($data?->workspaces as $workspaceArray){
                $this->workspaces[] = new AsanaWorkspace($workspaceArray, $this->objectFactory);
            }
        }
    }

    /**
     * @param ObjectFactory $objectFactory
     * @return void
     */
    public function initialise(
        ObjectFactory $objectFactory,
    ): void
    {
        parent::initialise($objectFactory);

        foreach ($this->workspaces as $workspace){
            $workspace->initialise($objectFactory);
        }
    }

    /**
     * @return void
     */
    public function destroy(
    ): void
    {
        parent::destroy();

        foreach ($this->workspaces as $workspace){
            $workspace->destroy();
        }
    }


    /**
     * @return void
     * @throws Exception
     */
    protected function loadDetails(
    ): void
    {
        $data = $this->objectFactory->create(AsanaUserCommand::class)->loadUser($this->id);

        $this->email = $data->email ?? null;
        $this->avatar = $data->photo->image_128x128 ?? null;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getEmail(
    ): string
    {
        if ($this->email === null){
            $this->load();
        }
        
        return $this->email ?? '';
    }

    /**
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * @return AsanaWorkspace[]
     */
    public function getWorkspaces(
    ): array
    {
        return $this->workspaces;
    }
}