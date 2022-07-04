<?php
namespace CarloNicora\Minimalism\Services\Asana\Data;

use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaObject;

class AsanaSection extends AbstractAsanaObject
{
    /**
     * @return void
     */
    protected function loadDetails(
    ): void
    {
        //$data = $this->objectFactory->create(AsanaSectionCommand::class)->loadProject($this->id);
    }

    /**
     * @return bool
     */
    public function canBeToday(
    ): bool
    {
        return str_contains(haystack: strtolower($this->getName()??''), needle: 'today');
    }
}