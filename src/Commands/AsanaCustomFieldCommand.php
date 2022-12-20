<?php

namespace CarloNicora\Minimalism\Services\Asana\Commands;

use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaCommand;
use CarloNicora\Minimalism\Services\Asana\Data\AsanaCustomField;

class AsanaCustomFieldCommand extends AbstractAsanaCommand
{
    /**
     * @param string $workspaceId
     * @return AsanaCustomField[]
     */
    public function getCustomFieldList(
        string $workspaceId,
    ): array
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        return $this->factory->createFromList(
            type: AsanaCustomField::class,
            iterator: $this->client->custom_fields->getCustomFieldsForWorkspace(
                workspace_gid: $workspaceId,
            ),
        );
    }

    /**
     * @param string $customFieldId
     * @return AsanaCustomField
     */
    public function getCustomField(
        string $customFieldId,
    ): AsanaCustomField
    {
        return $this->factory->create(
            type: AsanaCustomField::class,
            data: $this->client->custom_fields->getCustomField(
                custom_field_gid: $customFieldId,
                params: ['opt_fields' => 'enum_options,multi_enum_values',],
            ),
        );
    }
}