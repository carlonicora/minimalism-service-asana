<?php
namespace CarloNicora\Minimalism\Services\Asana;

use RuntimeException;

class Asana
{
    /**
     * @param string|null $MINIMALISM_SERVICE_ASANA_TOKEN
     * @param string|null $MINIMALISM_SERVICE_ASANA_CLIENT_ID
     * @param string|null $MINIMALISM_SERVICE_ASANA_CLIENT_SECRET
     * @param string|null $MINIMALISM_SERVICE_ASANA_REDIRECT
     */
    public function __construct(
        private ?string $MINIMALISM_SERVICE_ASANA_TOKEN=null,
        private ?string $MINIMALISM_SERVICE_ASANA_CLIENT_ID=null,
        private ?string $MINIMALISM_SERVICE_ASANA_CLIENT_SECRET=null,
        private ?string $MINIMALISM_SERVICE_ASANA_REDIRECT=null,
    )
    {
        if (
            $this->MINIMALISM_SERVICE_ASANA_TOKEN === null
            && $this->MINIMALISM_SERVICE_ASANA_CLIENT_ID === null
            && $this->MINIMALISM_SERVICE_ASANA_CLIENT_SECRET === null
            && $this->MINIMALISM_SERVICE_ASANA_REDIRECT === null
        ){
            throw new RuntimeException('Minimalism service asana requires either a personal access token or a client in its configurations', 500);
        }
    }
}