<?php
namespace CarloNicora\Minimalism\Services\Asana\Models\Asana;

use CarloNicora\Minimalism\Abstracts\AbstractModel;
use CarloNicora\Minimalism\Enums\HttpCode;
use CarloNicora\Minimalism\Services\Asana\Asana;
use CarloNicora\Minimalism\Services\Path;
use JetBrains\PhpStorm\NoReturn;

class Start extends AbstractModel
{
    /**
     * @param Path $path
     * @param Asana $asana
     * @return HttpCode
     */
    #[NoReturn] public function get(
        Path $path,
        Asana $asana,
    ): HttpCode
    {
        $state = null;
        $url = $asana->getClient()->dispatcher->authorizationUrl($state);

        $_SESSION['asana_state'] = $state;
        $_SESSION['asana_redirect'] = $path->getUrl();

        header('Location:' . $url);
        exit;
    }
}