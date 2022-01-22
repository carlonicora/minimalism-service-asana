<?php
namespace CarloNicora\Minimalism\Services\Asana\Commands;

use CarloNicora\Minimalism\Enums\HttpCode;
use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaCommand;
use CarloNicora\Minimalism\Services\Asana\Data\AsanaUser;
use RuntimeException;
use stdClass;

class AsanaUserCommand extends AbstractAsanaCommand
{
    /**
     * @param string $userId
     * @return stdClass
     */
    public function loadUser(
        string $userId,
    ): stdClass
    {
        /** @var stdClass $response */
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $response = $this->client->users->getUser(
            user_gid: $userId,
        );

        return $response;
    }

    /**
     * @param string $userId
     * @return AsanaUser
     */
    public function getUser(
        string $userId='me',
    ): AsanaUser
    {
        return $this->factory->create(
            type: AsanaUser::class,
            data: $this->client->users->getUser(
                user_gid: $userId
            ),
        );
    }

    /**
     * @param string|null $teamId
     * @param string|null $workspaceId
     * @return AsanaUser[]
     */
    public function getUsers(
        ?string $teamId=null,
        ?string $workspaceId=null,
    ): array
    {
        if ($teamId !== null){
            $response = $this->factory->createFromList(
                type: AsanaUser::class,
                iterator: $this->client->users->getUsersForTeam(
                    team_gid: $teamId,
                ),
            );
        } elseif ($workspaceId !== null) {
            $response = $this->factory->createFromList(
                type: AsanaUser::class,
                iterator: $this->client->users->getUsersForWorkspace(
                    workspace_gid: $workspaceId,
                ),
            );
        } else {
            throw new RuntimeException('', HttpCode::NotImplemented->value);
        }

        return $response;
    }
}